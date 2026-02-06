<?php

namespace wcbel\classes\services\history;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\History;
use wcbel\classes\services\background_process\ProductBackgroundProcess;
use wcbel\classes\services\product_update\Update_Service;

class HistoryUndoService
{
    const MAX_PROCESS_COUNT = 100;

    private $history_repository;
    private $is_processing;
    private $history_id;
    private $product_ids;
    private $total_count;
    private $complete_actions;

    public function __construct()
    {
        $this->is_processing = false;
        $this->history_repository = History::get_instance();
    }

    public function is_processing()
    {
        return $this->is_processing;
    }

    public function get_total_tasks()
    {
        return $this->total_count;
    }

    public function set_data($data)
    {
        $this->history_id = (!empty($data['history_id'])) ? intval($data['history_id']) : 0;
        $this->complete_actions = (!empty($data['complete_actions'])) ? $data['complete_actions'] : null;
    }

    public function perform()
    {
        if (empty($this->history_id)) {
            $this->do_complete_actions(false);
            return false;
        }

        $this->product_ids = [];

        $this->total_count = $this->history_repository->get_history_items_total_count($this->history_id, 'prev_total_count');
        if ($this->total_count == 0) {
            $this->do_complete_actions(false);
            return false;
        }

        if ($this->total_count > self::MAX_PROCESS_COUNT && ProductBackgroundProcess::is_enable()) {
            return $this->push_to_queue();
        } else {
            return $this->undo();
        }
    }

    private function get_product_data($data)
    {
        return [
            [
                'name' => $data['field']['name'],
                'sub_name' => (!empty($data['field']['sub_name'])) ? $data['field']['sub_name'] : '',
                'type' => $data['field']['type'],
                'action' => !empty($data['field']['action']) ? $data['field']['action'] : '',
                'deleted_ids' => !empty($data['field']['deleted_ids']) ? $data['field']['deleted_ids'] : [],
                'operator' => '',
                'rest' => (isset($data['rest']) && $data['rest'] === true),
                'background_process' => (isset($data['background_process']) && $data['background_process'] === true),
                'used_for_variations' => (!empty($data['field']['extra_fields']['used_for_variations']['prev'])) ? $data['field']['extra_fields']['used_for_variations']['prev'] : null,
                'attribute_is_visible' => (!empty($data['field']['extra_fields']['attribute_is_visible']['prev'])) ? $data['field']['extra_fields']['attribute_is_visible']['prev'] : null,
                'value' => $data['value'],
                'revert_mode' => true,
                'operation' => 'inline_edit',
            ]
        ];
    }

    public function get_product_ids()
    {
        return $this->product_ids;
    }

    private function push_to_queue()
    {
        $background_process = ProductBackgroundProcess::get_instance();
        if ($background_process->is_not_queue_empty()) {
            return false;
        }

        $this->is_processing = true;
        $per_page = 1500;
        if ($this->total_count >= $per_page) {
            $max_page = ceil(intval($this->total_count) / $per_page);
        } else {
            $max_page = 1;
        }

        $rest = 1;
        for ($i = 1; $i <= $max_page; $i++) {
            $offset = ($per_page * $i) - $per_page;
            $history_items = $this->history_repository->get_history_rows($this->history_id, [
                'columns' => ['historiable_id', 'field', 'prev_value', 'prev_total_count'],
                'limit' => $per_page,
                'offset' => $offset,
                'orderby' => 'DESC'
            ]);
            if (empty($history_items)) {
                continue;
            }

            $j = 1;
            $loop_i = 1;
            foreach ($history_items as $item) {
                if (!in_array($item->historiable_id, $this->product_ids)) {
                    $rest = 1;
                    $this->product_ids[] = intval($item->historiable_id);
                }

                $item->prev_value = is_serialized($item->prev_value) ? unserialize($item->prev_value) : $item->prev_value;
                if (!is_array($item->prev_value) && $item->prev_value == 'wcbe_bp_rest') {
                    continue;
                }

                $field = unserialize($item->field);
                if (isset($item->prev_total_count) && intval($item->prev_total_count) > self::MAX_PROCESS_COUNT) {
                    if (!isset($field['action'])) {
                        $field['action'] = 'default';
                    }
                    $value = $this->get_value($field['action'], $item->prev_value);
                    if (empty($value) || !is_array($value)) {
                        continue;
                    }
                    $items_grouped = array_chunk($value, self::MAX_PROCESS_COUNT);
                    if (!empty($items_grouped)) {
                        foreach ($items_grouped as $key => $items) {
                            $prev_value = $item->prev_value;
                            if (isset($prev_value['variations'])) {
                                $prev_value['variations'] = $items;
                            } else {
                                if (isset($prev_value['combinations'])) {
                                    $prev_value['combinations'] = $items;
                                }
                            }

                            $background_process->push_to_queue([
                                'handler' => 'history_undo',
                                'product_id' => intval($item->historiable_id),
                                'update_type' => ($field['type'] == 'variation') ? 'variation' : 'product',
                                'product_data' => $this->get_product_data([
                                    'field' => $field,
                                    'value' => $prev_value,
                                    'rest' => ($rest > 1),
                                    'background_process' => true
                                ]),
                                'history_id' => $this->history_id,
                            ]);
                            $background_process->save();
                            $rest++;
                        }
                    }
                } else {
                    $background_process->push_to_queue([
                        'handler' => 'history_undo',
                        'update_type' => ($field['type'] == 'variation') ? 'variation' : 'product',
                        'product_data' => $this->get_product_data([
                            'field' => $field,
                            'value' => $item->prev_value,
                            'rest' => ($rest > 1),
                            'background_process' => true
                        ]),
                        'product_id' => intval($item->historiable_id),
                        'history_id' => $this->history_id,
                    ]);
                    if ($j == self::MAX_PROCESS_COUNT || $loop_i == count($history_items)) {
                        $background_process->save();
                        $j = 0;
                    }
                    $j++;
                    $rest++;
                }

                $loop_i++;
            }
        }

        $background_process->set_total_tasks($this->total_count);
        $background_process->add_complete_action([
            'handler' => 'history_undo',
            'data' => [
                'history_id' => $this->history_id
            ]
        ]);

        if (!empty($this->complete_actions)) {
            foreach ($this->complete_actions as $action) {
                $background_process->add_complete_action($action);
            }
        }

        $background_process->start();
        return true;
    }

    private function undo()
    {
        $history_items = $this->history_repository->get_history_rows($this->history_id, [
            'columns' => ['historiable_id', 'field', 'prev_value'],
            'orderby' => 'DESC'
        ]);

        if (empty($history_items) || !is_array($history_items)) {
            $this->do_complete_actions(false);
            return false;
        }

        $update_service = Update_Service::get_instance();
        foreach ($history_items as $item) {
            $item->prev_value = is_serialized($item->prev_value) ? unserialize($item->prev_value) : $item->prev_value;
            if (!is_array($item->prev_value) && $item->prev_value == 'wcbe_bp_rest') {
                continue;
            }
            if (!in_array($item->historiable_id, $this->product_ids)) {
                $this->product_ids[] = intval($item->historiable_id);
            }
            $field = unserialize($item->field);
            if (empty($field['name'])) {
                continue;
            }

            $update_service->set_update_data([
                'update_type' => ($field['type'] == 'variation') ? 'variation' : 'product',
                'product_ids' => [intval($item->historiable_id)],
                'product_data' => $this->get_product_data([
                    'field' => $field,
                    'value' => $item->prev_value,
                    'rest' => false,
                    'background_process' => false
                ]),
                'save_history' => false,
            ]);

            $update_service->perform();
        }

        $this->history_repository->update_history($this->history_id, ['reverted' => 1]);

        $this->do_complete_actions(true);
        return true;
    }

    private function get_value($action, $value_item)
    {
        $value = [];

        $value_item = is_serialized($value_item) ? unserialize($value_item) : $value_item;
        switch ($action) {
            case 'replace_variations':
                if (isset($value_item['combinations'])) {
                    $value = $value_item['combinations'];
                } else {
                    if (isset($value_item['variations'])) {
                        $value = $value_item['variations'];
                    }
                }
                break;
            case 'add_variations':
                // 
                break;
            default:
                $value = $value_item;
                break;
        }

        return $value;
    }

    private function do_complete_actions($result)
    {
        if (!empty($this->complete_actions)) {
            foreach ($this->complete_actions as $action) {
                if (!empty($action['hook'])) {
                    if (!empty($action['data'])) {
                        $action['data']['result'] = $result;
                        do_action($action['hook'], $action['data']);
                    } else {
                        do_action($action['hook']);
                    }
                }
            }
        }
    }
}
