<?php

namespace wcbel\classes\repositories\search;

class Search_Main
{
    protected $filter_profile_option_name;
    protected $use_always_table;
    protected $current_data_option_name;

    public function update(array $data)
    {
        if (!isset($data['key'])) {
            return false;
        }

        $presets = $this->get_presets();
        $presets[$data['key']] = $data;
        return update_option($this->filter_profile_option_name, $presets);
    }

    public function delete($preset_key)
    {
        $presets = $this->get_presets();
        if (is_array($presets) && array_key_exists($preset_key, $presets)) {
            unset($presets[$preset_key]);
        }
        return update_option($this->filter_profile_option_name, $presets);
    }

    public function get_preset($preset_key)
    {
        $presets = $this->get_presets();
        return (isset($presets[esc_sql($preset_key)])) ? $presets[esc_sql($preset_key)] : false;
    }

    public function get_presets()
    {
        return get_option($this->filter_profile_option_name);
    }

    public function update_use_always(string $preset_key, string $option_name = '')
    {
        $option_name = (!empty($option_name)) ? esc_sql($option_name) : $this->use_always_table;
        return update_option($option_name, esc_sql($preset_key));
    }

    public function get_use_always()
    {
        return get_option($this->use_always_table);
    }

    public function get_current_data()
    {
        return get_option($this->current_data_option_name);
    }

    public function update_current_data($current_data)
    {
        if (empty($current_data) || !is_array($current_data)) {
            return false;
        }

        $old_current_data = $this->get_current_data();
        if (empty($old_current_data) || !is_array($old_current_data)) {
            $old_current_data = [];
        }
        foreach ($current_data as $data_key => $data_value) {
            $old_current_data[esc_sql($data_key)] = esc_sql($data_value);
        }

        return update_option($this->current_data_option_name, $old_current_data);
    }

    public function delete_current_data()
    {
        return delete_option($this->current_data_option_name);
    }

    public function has_search_options()
    {
        $filters = get_option($this->filter_profile_option_name);
        $use_always = get_option($this->use_always_table);

        return (!empty($filters) && !empty($use_always));
    }
}
