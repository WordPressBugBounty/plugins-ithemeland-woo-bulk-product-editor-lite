"use strict";

var tableIsLoading = false;

function wcbeGetProductGalleryImages(productsId) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_gallery_images",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productsId,
        },
        success: function (response) {
            if (response.success && response.images !== "") {
                jQuery("#wcbe-modal-gallery-items").html(response.images);
            }
        },
        error: function () { },
    });
}

function wcbeGetProductsChecked() {
    if (wcbeSelectAllChecked()) {
        return "all_filtered";
    } else {
        let productIds = [];
        let productsChecked = jQuery("input.wcbe-check-item:visible:checkbox:checked");
        if (productsChecked.length > 0) {
            productIds = productsChecked
                .map(function (i) {
                    return jQuery(this).val();
                })
                .get();
        }
        return productIds;
    }
}

function wcbeGetProductsCheckedWithoutVariations() {
    if (jQuery('.wcbe-check-item-main[value="all"]').prop("checked") === true || !jQuery("input.wcbe-check-item:visible:checkbox:checked").length) {
        return "all_filtered";
    } else {
        let productIds = [];
        let productsChecked = jQuery("input.wcbe-check-item:visible:checkbox:checked");
        if (productsChecked.length > 0) {
            productIds = productsChecked
                .map(function (i) {
                    if (jQuery(this).attr("data-item-type") !== "variation") {
                        return jQuery(this).val();
                    }
                })
                .get();
        }
        return productIds;
    }
}

function wcbeReloadProducts(edited_ids = [], current_page = wcbeGetCurrentPage()) {
    let data = wcbeGetCurrentFilterData();
    wcbeHistoryFilter(null, false);
    wcbeProductsFilter(data, "pro_search", edited_ids, current_page);
}

function wcbeProductsFilter(data, action, edited_ids = [], page = wcbeGetCurrentPage()) {
    if (action === "pagination") {
        wcbePaginationLoadingStart();
    } else {
        wcbeLoadingStart();
    }

    if (WCBE_DATA.wcbe_settings.close_popup_after_applying == "yes") {
        wcbeCloseFloatSideModal();
    }

    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_products_filter",
            nonce: WCBE_DATA.ajax_nonce,
            filter_data: data,
            current_page: page,
            search_action: action,
            option_values: wcbeGetFilterFormOptionValues(),
        },
        success: function (response) {
            jQuery("#wcbe-filter-form-get-products").prop("disabled", false);
            jQuery("#wcbe-filter-form-reset").prop("disabled", false);
            goToPageProcessing = false;
            if (response.success) {
                wcbeLoadingSuccess();
                wcbeSetProductsList(response, edited_ids);
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            jQuery("#wcbe-filter-form-get-products").prop("disabled", false);
            jQuery("#wcbe-filter-form-reset").prop("disabled", false);
            goToPageProcessing = false;
            wcbeLoadingError();
        },
    });
}

function wcbeGetBulkEditData() {
    let productData = [];
    let productIds = wcbeGetProductsChecked();

    jQuery("#wcbe-float-side-modal-bulk-edit .wcbe-form-group").each(function () {
        let value;
        if (jQuery(this).find("[data-field=value]").length > 1) {
            value = jQuery(this)
                .find("[data-field=value]")
                .map(function () {
                    if (jQuery(this).val() !== "") {
                        return jQuery(this).val();
                    }
                })
                .get();
        } else {
            value = jQuery(this).find("[data-field=value]").val();
        }

        if (typeof jQuery(this).attr("data-name") != "undefined") {
            if (
                (jQuery.isArray(value) && value.length > 0) ||
                (!jQuery.isArray(value) && value != "" && typeof value != "undefined") ||
                (jQuery(this).find('select[data-field="used_for_variations"]').length > 0 && jQuery(this).find('select[data-field="used_for_variations"]').val() != "") ||
                (jQuery(this).find('select[data-field="attribute_is_visible"]').length > 0 && jQuery(this).find('select[data-field="attribute_is_visible"]').val() != "") ||
                jQuery.inArray(jQuery(this).find("[data-field=operator]").val(), ["text_remove_duplicate", "number_clear", "text_clear"]) !== -1
            ) {
                let name = jQuery(this).attr("data-name");
                let type = jQuery(this).attr("data-type");

                if (jQuery(this).find("[data-field=operator]").val() == "text_remove_duplicate") {
                    name = "remove_duplicate";
                    type = "remove_duplicate";
                    value = "trash";
                    if (jQuery.isArray(productIds) && productIds.length < 1) {
                        productIds = [0];
                    }
                }

                productData.push({
                    name: name,
                    sub_name: jQuery(this).attr("data-sub-name") ? jQuery(this).attr("data-sub-name") : "",
                    type: type,
                    operator: jQuery(this).find("[data-field=operator]").val(),
                    value: value,
                    replace: jQuery(this).find("[data-field=replace]").val(),
                    sensitive: jQuery(this).find("[data-field=sensitive]").val(),
                    round: jQuery(this).find("[data-field=round]").val(),
                    used_for_variations: jQuery(this).find('select[data-field="used_for_variations"]').val(),
                    attribute_is_visible: jQuery(this).find('select[data-field="attribute_is_visible"]').val(),
                    operation: "bulk_edit",
                });
            }
        }
    });

    if (jQuery(".wcbe-bulk-edit-custom-field-file-item").length) {
        let customFieldFiles = [];
        let containerElement = jQuery(".wcbe-bulk-edit-custom-field-file-item").first().closest(".wcbe-form-group");
        jQuery(".wcbe-bulk-edit-custom-field-file-item").each(function () {
            let fileElement = jQuery(this);
            customFieldFiles.push({
                name: fileElement.find("input.wcbe-bulk-edit-file-name").val(),
                url: fileElement.find("input.wcbe-bulk-edit-file-url").val(),
            });
        });

        if (customFieldFiles.length) {
            productData.push({
                name: containerElement.attr("data-name"),
                sub_name: "",
                type: containerElement.attr("data-type"),
                operator: "",
                value: customFieldFiles,
                operation: "bulk_edit",
            });
        }
    }

    return productData;
}

function wcbeReloadTable(productIds = []) {
    jQuery("#wcbe-items-table tbody").html(
        '<td colspan="100%" style="text-align: center;"><img style="vertical-align: middle; padding: 5px 0;" width="22" height="22" src="' + WCBE_DATA.icons.loading_2 + '"></td>'
    );
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_products_filter",
            nonce: WCBE_DATA.ajax_nonce,
            filter_data: wcbeGetCurrentFilterData(),
            current_page: wcbeGetCurrentPage(),
        },
        success: function (response) {
            wcbeSetProductsList(response, productIds);
            wcbeHistoryFilter(null, false);
        },
        error: function () { },
    });
}

function wcbeGetFilterFormOptionValues() {
    let optionValues = {};
    if (jQuery(".wcbe-filter-form-select2-option-values").length) {
        jQuery(".wcbe-filter-form-select2-option-values option:selected").each(function () {
            if (jQuery(this).attr("value") != "" && jQuery(this).attr("value") != null) {
                let optionName = jQuery(this).closest(".wcbe-filter-form-select2-option-values").attr("data-option-name");
                if (!optionValues[optionName]) {
                    optionValues[optionName] = {};
                }
                optionValues[optionName][jQuery(this).attr("value")] = jQuery(this).text();
            }
        });
    }
    return optionValues;
}

function wcbeClearFilterDataWithRedirect() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_clear_filter_data",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            window.location.search = "?page=wcbe";
        },
        error: function () { },
    });
}

function wcbeSetStatusFilter(statusFilters) {
    jQuery(".wcbe-top-nav-status-filter").html(statusFilters);

    jQuery(".wcbe-bulk-edit-status-filter-item").removeClass("active");
    let statusFilter = jQuery("#wcbe-filter-form-product-status").val() && jQuery("#wcbe-filter-form-product-status").val() != "" ? jQuery("#wcbe-filter-form-product-status").val() : "all";
    if (jQuery.isArray(statusFilter)) {
        statusFilter.forEach(function (val) {
            jQuery('.wcbe-bulk-edit-status-filter-item[data-status="' + val + '"]').addClass("active");
        });
    } else {
        let activeItem = jQuery('.wcbe-bulk-edit-status-filter-item[data-status="' + statusFilter + '"]');
        activeItem.addClass("active");
        jQuery(".wcbe-status-filter-selected-name").text(" - " + activeItem.text());
    }
}

function wcbeSetProductsList(response, edited_ids = []) {
    wcbeTotalProductCount = response.products_count;
    jQuery("#wcbe-bulk-edit-select-all-variations").prop("checked", false);
    jQuery(".wcbe-items-count").html(wcbeGetTableCount(jQuery("#wcbe-quick-per-page").val(), wcbeGetCurrentPage(), response.products_count));
    jQuery("#wcbe-items-table tbody")
        .html(response.rows)
        .ready(function () {
            if (wcbeSelectAllChecked()) {
                jQuery(".wcbe-check-item").prop("checked", true);
            } else {
                if (!edited_ids.length) {
                    jQuery('.wcbe-check-item-main[value="visible"]').prop("checked", false).change();
                }
            }

            if (edited_ids && edited_ids.length > 0) {
                jQuery("tr").removeClass("wcbe-item-edited");
                edited_ids.forEach(function (productID) {
                    jQuery('tr[data-item-id="' + productID + '"]').addClass("wcbe-item-edited");
                    jQuery('input[value="' + productID + '"]')
                        .prop("checked", true)
                        .change();
                });
                wcbeShowSelectionTools();
            } else {
                wcbeHideSelectionTools();
            }
        });
    wcbeSetStatusFilter(response.status_filters);
    jQuery(".wcbe-items-pagination")
        .html(response.pagination)
        .ready(function () {
            jQuery("#wcbe-top-nav-filters-go-to-page").attr("max", jQuery(".wcbe-top-nav-filters-paginate").attr("data-last-page"));

            if (parseInt(jQuery(".wcbe-top-nav-filters-paginate").attr("data-last-page")) < 2) {
                jQuery(".wcbe-table-item-selector-checkbox").addClass("wcbe-check-item-main");
                jQuery(".wcbe-table-item-selector").hide();
            } else {
                jQuery(".wcbe-table-item-selector-checkbox").removeClass("wcbe-check-item-main");
                jQuery(".wcbe-table-item-selector").show();
            }
        });

    wcbeReInitDatePicker();
    wcbeReInitColorPicker();
    wcbeSetTipsyTooltip();
}

function wcbeGetProductData(productID) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_data",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productID,
        },
        success: function (response) {
            if (response.success) {
                wcbeSetProductDataBulkEditForm(response.product_data);
            } else {
            }
        },
        error: function () { },
    });
}

function wcbeSetSelectedProducts(productIds) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_by_ids",
            nonce: WCBE_DATA.ajax_nonce,
            product_ids: productIds,
        },
        success: function (response) {
            if (response.success && response.products instanceof Object && Object.keys(response.products).length > 0) {
                let productsField = jQuery("#wcbe-select-products-value");
                if (productsField.length > 0) {
                    jQuery.each(response.products, function (productId, productTitle) {
                        productsField.append("<option value='" + productId + "' selected>" + productTitle + "</option>").prop("selected", true);
                    });
                }
            }
        },
        error: function () { },
    });
}

function wcbeSetProductDataBulkEditForm(productData) {
    let reviews_allowed = productData.reviews_allowed ? "yes" : "no";
    let sold_individually = productData.sold_individually ? "yes" : "no";
    let manage_stock = productData.manage_stock ? "yes" : "no";
    let featured = productData.featured ? "yes" : "no";
    let virtual = productData.virtual ? "yes" : "no";
    let downloadable = productData.downloadable ? "yes" : "no";

    let attributes = jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-group[data-type="attribute"]');
    if (attributes.length > 0) {
        let attribute_name = "";
        attributes.each(function () {
            attribute_name = jQuery(this).attr("data-name");
            if (productData.attribute[attribute_name]) {
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-group[data-type="attribute"][data-name="' + attribute_name + '"]')
                    .find('select[data-field="value"]')
                    .val(productData.attribute[attribute_name])
                    .change();
            }
        });
    }

    let custom_fields = jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-group[data-type="custom_fields"]');
    if (custom_fields.length > 0) {
        let taxonomy_name = "";
        custom_fields.each(function () {
            taxonomy_name = jQuery(this).attr("data-name");
            if (productData.meta_field[taxonomy_name]) {
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-group[data-type="custom_fields"][data-name="' + taxonomy_name + '"]')
                    .find('[data-field="value"]')
                    .val(productData.meta_field[taxonomy_name][0])
                    .change();
            }
        });
    }

    jQuery("#wcbe-bulk-edit-form-product-title").val(productData.name);
    jQuery("#wcbe-bulk-edit-form-product-slug").val(productData.slug);
    jQuery("#wcbe-bulk-edit-form-product-sku").val(productData.sku);
    jQuery("#wcbe-bulk-edit-form-product-description").val(productData.post_content);
    jQuery("#wcbe-bulk-edit-form-product-short-description").val(productData.post_excerpt);
    jQuery("#wcbe-bulk-edit-form-product-purchase-note").val(productData.purchase_note);
    jQuery("#wcbe-bulk-edit-form-product-menu-order").val(productData.menu_order);
    jQuery("#wcbe-bulk-edit-form-product-sold-individually").val(sold_individually).change();
    jQuery("#wcbe-bulk-edit-form-product-enable-reviews").val(reviews_allowed).change();
    jQuery("#wcbe-bulk-edit-form-product-product-status").val(productData.status).change();
    jQuery("#wcbe-bulk-edit-form-product-catalog-visibility").val(productData.catalog_visibility).change();
    jQuery("#wcbe-bulk-edit-form-product-date-created").val(productData.post_date);
    jQuery("#wcbe-bulk-edit-form-product-author").val(productData.post_author).change();
    jQuery("#wcbe-bulk-edit-form-categories").val(productData.product_cat).change();
    jQuery("#wcbe-bulk-edit-form-tags").val(productData.product_tag).change();
    jQuery("#wcbe-bulk-edit-form-regular-price").val(productData.regular_price);
    jQuery("#wcbe-bulk-edit-form-sale-price").val(productData.sale_price);
    jQuery("#wcbe-bulk-edit-form-sale-date-from").val(productData.date_on_sale_from);
    jQuery("#wcbe-bulk-edit-form-sale-date-to").val(productData.date_on_sale_to);
    jQuery("#wcbe-bulk-edit-form-tax-status").val(productData.tax_status).change();
    jQuery("#wcbe-bulk-edit-form-tax-class").val(productData.tax_class).change();
    jQuery("#wcbe-bulk-edit-form-shipping-class").val(productData.shipping_class).change();
    jQuery("#wcbe-bulk-edit-form-width").val(productData.width);
    jQuery("#wcbe-bulk-edit-form-height").val(productData.height);
    jQuery("#wcbe-bulk-edit-form-length").val(productData.length);
    jQuery("#wcbe-bulk-edit-form-weight").val(productData.weight);
    jQuery("#wcbe-bulk-edit-form-manage-stock").val(manage_stock).change();
    jQuery("#wcbe-bulk-edit-form-stock-status").val(productData.stock_status).change();
    jQuery("#wcbe-bulk-edit-form-stock-quantity").val(productData.stock_quantity);
    jQuery("#wcbe-bulk-edit-form-backorders").val(productData.backorders).change();
    jQuery("#wcbe-bulk-edit-form-product-type").val(productData.product_type).change();
    jQuery("#wcbe-bulk-edit-form-featured").val(featured).change();
    jQuery("#wcbe-bulk-edit-form-virtual").val(virtual).change();
    jQuery("#wcbe-bulk-edit-form-downloadable").val(downloadable).change();
    jQuery("#wcbe-bulk-edit-form-download-limit").val(productData.download_limit);
    jQuery("#wcbe-bulk-edit-form-download-expiry").val(productData.download_expiry).change();
    jQuery("#wcbe-bulk-edit-form-product-url").val(productData.product_url);
    jQuery("#wcbe-bulk-edit-form-button-text").val(productData.button_text);
    jQuery("#wcbe-bulk-edit-form-upsells").val(productData.upsell_ids).change();
    jQuery("#wcbe-bulk-edit-form-cross-sells").val(productData.cross_sell_ids).change();
}

function wcbeDeleteProduct(productIDs, deleteType) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_delete_products",
            nonce: WCBE_DATA.ajax_nonce,
            product_ids: productIDs,
            delete_type: deleteType,
        },
        success: function (response) {
            if (response.success) {
                if (response.is_processing) {
                    wcbeIsProcessing();
                    wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                } else {
                    wcbeReloadProducts();
                    wcbeHideSelectionTools();
                    wcbeCheckUndoRedoStatus(response.reverted, response.history_items);
                    jQuery(".wcbe-history-items tbody").html(response.history_items);
                    jQuery(".wcbe-history-pagination-container").html(response.history_pagination);
                }
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeRestoreProduct(productIDs) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_untrash_products",
            nonce: WCBE_DATA.ajax_nonce,
            product_ids: productIDs,
        },
        success: function (response) {
            if (response.success) {
                if (response.is_processing) {
                    wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                    wcbeIsProcessing();
                } else {
                    wcbeReloadProducts();
                    wcbeHideSelectionTools();
                }
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeEmptyTrash() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_empty_trash",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                if (response.is_processing) {
                    wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                    wcbeIsProcessing();
                } else {
                    wcbeReloadProducts();
                    wcbeHideSelectionTools();
                }
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeDuplicateProduct(productIDs, duplicateNumber) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_duplicate_product",
            nonce: WCBE_DATA.ajax_nonce,
            product_ids: productIDs,
            count: duplicateNumber,
        },
        success: function (response) {
            jQuery("#wcbe-bulk-edit-duplicate-start").prop("disabled", false);
            if (response.success) {
                if (response.is_processing) {
                    wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                    wcbeIsProcessing();
                } else {
                    wcbeReloadProducts([], wcbeGetCurrentPage());
                    wcbeCloseModal();
                    wcbeHideSelectionTools();
                }
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            jQuery("#wcbe-bulk-edit-duplicate-start").prop("disabled", false);
            wcbeLoadingError();
        },
    });
}

function wcbeCreateNewProduct(count = 1, productData = {}) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_create_new_product",
            nonce: WCBE_DATA.ajax_nonce,
            count: count,
            productData: productData,
        },
        success: function (response) {
            jQuery("#wcbe-create-new-item").prop("disabled", false);
            if (response.success) {
                if (response.is_processing) {
                    wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                    wcbeIsProcessing();
                } else {
                    wcbeReloadProducts([], 1);
                    setTimeout(function () {
                        wcbeCloseFloatSideModal();
                    }, 1000);
                }
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            jQuery("#wcbe-create-new-item").prop("disabled", false);
            wcbeLoadingError();
        },
    });
}

function wcbeHideVariationSelectionTools() {
    jQuery("#wcbe-bulk-edit-select-all-variations-tools").hide();
}

function wcbeShowVariationSelectionTools() {
    jQuery("#wcbe-bulk-edit-select-all-variations-tools").show();
}

function wcbeSaveColumnProfile(presetKey, items, type) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_save_column_profile",
            nonce: WCBE_DATA.ajax_nonce,
            preset_key: presetKey,
            items: items,
            type: type,
        },
        success: function (response) {
            if (response.success) {
                wcbeLoadingSuccess();
                location.href = location.href.replace(location.hash, "");
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeLoadFilterProfile(presetKey) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_load_filter_profile",
            nonce: WCBE_DATA.ajax_nonce,
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                if (response.option_values) {
                    WCBE_DATA.filter_option_values = response.option_values;
                }
                wcbeResetFilterForm();
                wcbeLoadingSuccess();
                wcbeSetProductsList(response);
                wcbeCloseModal();

                setTimeout(function () {
                    setFilterValues(response.filter_data);
                }, 500);
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeDeleteFilterProfile(presetKey) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_delete_filter_profile",
            nonce: WCBE_DATA.ajax_nonce,
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                wcbeLoadingSuccess();
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeFilterProfileChangeUseAlways(presetKey) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_filter_profile_change_use_always",
            nonce: WCBE_DATA.ajax_nonce,
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                jQuery('.wcbe-bulk-edit-filter-profile-load[value="' + presetKey + '"]').trigger("click");
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeGetCurrentFilterData() {
    return jQuery("#wcbe-quick-search-text").val() ? wcbeGetQuickSearchData() : wcbeGetProSearchData();
}

function wcbeResetQuickSearchForm() {
    jQuery(".wcbe-top-nav-filters-search input").val("");
    jQuery(".wcbe-top-nav-filters-search select").prop("selectedIndex", 0);
    jQuery("#wcbe-quick-search-reset").hide();
    jQuery(".wcbe-quick-filter a").removeClass("active");
}

function wcbeResetFilterForm() {
    jQuery(".wcbe-reset-filter-form").closest("li").hide();

    jQuery("#wcbe-float-side-modal-filter input").val("").change();
    jQuery("#wcbe-float-side-modal-filter textarea").val("").change();
    jQuery('#wcbe-float-side-modal-filter select[data-type="operator"]').prop("selectedIndex", 0);
    jQuery('#wcbe-float-side-modal-filter select[data-field="value"]').val("").change();
    jQuery('#wcbe-float-side-modal-filter select[data-field="from"]').val("").change();
    jQuery('#wcbe-float-side-modal-filter select[data-field="to"]').val("").change();
    jQuery("#wcbe-float-side-modal-filter .wcbe-select2").val("").trigger("change");
    jQuery("#wcbe-float-side-modal-filter .select2-hidden-accessible").val("").trigger("change");
    jQuery("#wcbe-float-side-modal-filter .wcbe-select2-item").val("").trigger("change");
    jQuery(".wcbe-bulk-edit-status-filter-item").removeClass("active");
}

function wcbeResetFilters() {
    wcbeResetFilterForm();
    wcbeResetQuickSearchForm();
    jQuery(".wcbe-reset-filter-form").closest("li").hide();
    jQuery(".wcbe-check-item-main").prop("checked", false).change();

    setTimeout(function () {
        if (window.location.search !== "?page=wcbe") {
            wcbeClearFilterDataWithRedirect();
        } else {
            let data = wcbeGetCurrentFilterData();
            wcbeProductsFilter(data, "pro_search");
        }
    }, 250);
}

function wcbeChangeCountPerPage(countPerPage) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_change_count_per_page",
            nonce: WCBE_DATA.ajax_nonce,
            count_per_page: countPerPage,
        },
        success: function (response) {
            if (response.success) {
                wcbeReloadProducts([], 1);
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeAddProductTaxonomy(taxonomyInfo, taxonomyName, productId) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_add_product_taxonomy",
            nonce: WCBE_DATA.ajax_nonce,
            taxonomy_info: taxonomyInfo,
            taxonomy_name: taxonomyName,
        },
        success: function (response) {
            if (response.success) {
                wcbeGetProductTaxonomyTerms(productId, taxonomyName);
                wcbeLoadingSuccess();
                wcbeCloseModal();
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeAddProductAttribute(attributeInfo, attributeName) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_add_product_attribute",
            nonce: WCBE_DATA.ajax_nonce,
            attribute_info: attributeInfo,
            attribute_name: attributeName,
        },
        success: function (response) {
            if (response.success) {
                wcbeGetProductAttributeTerms(attributeInfo.product_id, attributeName);
                wcbeLoadingSuccess();
                wcbeCloseModal();
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeAddNewFileItem() {
    jQuery("#wcbe-modal-select-files-loading").show();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_add_new_file_item",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-modal-select-files .wcbe-inline-select-files").prepend(response.file_item);
                wcbeSetTipsyTooltip();
            }
            jQuery("#wcbe-modal-select-files-loading").hide();
        },
        error: function () {
            jQuery("#wcbe-modal-select-files-loading").hide();
        },
    });
}

function wcbeAddCustomFieldFileItem() {
    jQuery("#wcbe-modal-custom-field-files-loading").show();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_add_custom_field_file_item",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-modal-custom-field-files .wcbe-inline-custom-field-files").prepend(response.file_item);
                wcbeSetTipsyTooltip();
            }

            jQuery("#wcbe-modal-custom-field-files-loading").hide();
        },
        error: function () {
            jQuery("#wcbe-modal-custom-field-files-loading").hide();
        },
    });
}

function wcbeBulkEditAddCustomFieldFileItem() {
    jQuery("#wcbe-bulk-edit-custom-field-files-loading").show();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_bulk_edit_add_custom_field_file_item",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-custom-field-files").prepend(response.file_item);
                wcbeSetTipsyTooltip();
            }
            jQuery("#wcbe-bulk-edit-custom-field-files-loading").hide();
        },
        error: function () {
            jQuery("#wcbe-bulk-edit-custom-field-files-loading").hide();
        },
    });
}

function wcbeGetProductFiles(productID) {
    jQuery("#wcbe-modal-select-files-loading").show();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_files",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productID,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-modal-select-files .wcbe-inline-select-files").html(response.files);
                wcbeSetTipsyTooltip();
            } else {
                jQuery("#wcbe-modal-select-files .wcbe-inline-select-files").html("");
            }
            jQuery("#wcbe-modal-select-files-loading").hide();
        },
        error: function () {
            jQuery("#wcbe-modal-select-files .wcbe-inline-select-files").html("");
            jQuery("#wcbe-modal-select-files-loading").hide();
        },
    });
}

function wcbeGetProductCustomFieldFiles(productID, customFieldName) {
    jQuery("#wcbe-modal-custom-field-files-loading").show();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_custom_field_files",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productID,
            field_name: customFieldName,
        },
        success: function (response) {
            if (response.success) {
                jQuery(".wcbe-inline-custom-field-files").html(response.files);
            } else {
                jQuery(".wcbe-inline-custom-field-files").html("");
            }
            jQuery("#wcbe-modal-custom-field-files-loading").hide();
        },
        error: function () {
            jQuery(".wcbe-inline-custom-field-files").html("");
            jQuery("#wcbe-modal-custom-field-files-loading").hide();
        },
    });
}

function changedTabs(item) {
    let change = false;

    let tab = jQuery('nav.wcbe-tabs-navbar a[data-content="' + item.closest(".wcbe-tab-content-item").attr("data-content") + '"]');
    item
        .closest(".wcbe-tab-content-item")
        .find("[data-field=operator]")
        .each(function () {
            if (jQuery(this).val() === "text_remove_duplicate") {
                change = true;
                return false;
            }
        });
    item
        .closest(".wcbe-tab-content-item")
        .find('[data-field="value"], [data-field="from"], [data-field="to"]')
        .each(function () {
            if (jQuery(this).val() && jQuery(this).val() != "") {
                change = true;
                return false;
            }
        });

    if (change === true) {
        tab.addClass("wcbe-tab-changed");
    } else {
        tab.removeClass("wcbe-tab-changed");
    }
}

function wcbeCheckResetFilterButton() {
    jQuery(".wcbe-reset-filter-form").closest("li").hide();

    if (
        jQuery('#wcbe-bulk-edit-filter-tabs-contents [data-field="value"], #wcbe-bulk-edit-filter-tabs-contents [data-field="from"], #wcbe-bulk-edit-filter-tabs-contents [data-field="to"]').length > 0
    ) {
        jQuery('#wcbe-bulk-edit-filter-tabs-contents [data-field="value"], #wcbe-bulk-edit-filter-tabs-contents [data-field="from"], #wcbe-bulk-edit-filter-tabs-contents [data-field="to"]').each(
            function () {
                if (jQuery(this).val() != "" && jQuery(this).val() != null) {
                    jQuery(".wcbe-reset-filter-form").closest("li").show();
                    return true;
                }
            }
        );
    }
}

function wcbeGetQuickSearchData() {
    return {
        search_type: "quick_search",
        quick_search_text: jQuery("#wcbe-quick-search-text").val(),
        quick_search_field: jQuery("#wcbe-quick-search-field").val(),
        quick_search_operator: jQuery("#wcbe-quick-search-operator").val(),
    };
}

function wcbeSortByColumn(columnName, sortType) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_sort_by_column",
            nonce: WCBE_DATA.ajax_nonce,
            column_name: columnName,
            sort_type: sortType,
        },
        success: function (response) {
            if (response.success) {
                wcbeLoadingSuccess();
                wcbeSetProductsList(response);
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeColumnManagerFieldsGetForEdit(presetKey) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_column_manager_get_fields_for_edit",
            nonce: WCBE_DATA.ajax_nonce,
            preset_key: presetKey,
        },
        success: function (response) {
            jQuery("#wcbe-modal-column-manager-edit-preset .wcbe-box-loading").hide();
            jQuery(".wcbe-column-manager-added-fields[data-action=edit] .items").html(response.html);
            setTimeout(function () {
                wcbeSetColorPickerTitle();
            }, 250);
            jQuery(".wcbe-column-manager-available-fields[data-action=edit] li").each(function () {
                if (jQuery.inArray(jQuery(this).attr("data-name"), response.fields.split(",")) !== -1) {
                    jQuery(this).attr("data-added", "true").hide();
                } else {
                    jQuery(this).attr("data-added", "false").show();
                }
            });
            jQuery(".wcbe-color-picker").wpColorPicker();
        },
    });
}

function wcbeAddMetaKeysByProductID(productID) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "html",
        data: {
            action: "wcbe_add_meta_keys_by_product_id",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productID,
        },
        success: function (response) {
            jQuery("#wcbe-meta-fields-items").append(response);
            wcbeLoadingSuccess();
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeHistoryUndo() {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_history_undo",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.wcbe === false) {
                wcbeLoadingError(response.message);
            } else {
                if (response.success) {
                    if (response.is_processing) {
                        wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                        wcbeIsProcessing();
                    } else {
                        wcbeLoadingSuccess();
                        wcbeCheckUndoRedoStatus(response.reverted, response.history_items);
                        jQuery(".wcbe-history-items tbody").html(response.history_items);
                        jQuery(".wcbe-history-pagination-container").html(response.history_pagination);
                        wcbeReloadProducts(response.product_ids);
                    }
                }
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeHistoryRedo() {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_history_redo",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.wcbe === false) {
                wcbeLoadingError(response.message);
            } else {
                if (response.success) {
                    if (response.is_processing) {
                        wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                        wcbeIsProcessing();
                    } else {
                        wcbeLoadingSuccess();
                        wcbeCheckUndoRedoStatus(response.reverted, response.history_items);
                        jQuery(".wcbe-history-items tbody").html(response.history_items);
                        jQuery(".wcbe-history-pagination-container").html(response.history_pagination);
                        wcbeReloadProducts(response.product_ids);
                    }
                }
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeHistoryFilter(filters = null, loading = true) {
    if (loading === true) {
        wcbeLoadingStart();
    }

    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_history_filter",
            nonce: WCBE_DATA.ajax_nonce,
            filters: filters,
        },
        success: function (response) {
            if (response.success) {
                if (loading === true) {
                    wcbeLoadingSuccess();
                }
                wcbeCheckUndoRedoStatus(response.reverted, response.history_items);
                if (response.history_items) {
                    jQuery(".wcbe-history-items tbody").html(response.history_items);
                    jQuery(".wcbe-history-pagination-container").html(response.history_pagination);
                } else {
                    jQuery(".wcbe-history-items tbody").html("<td colspan='4'><span>" + wcbeTranslate.notFound + "</span></td>");
                }
            } else {
                if (loading === true) {
                    wcbeLoadingError();
                }
            }
        },
        error: function () {
            if (loading === true) {
                wcbeLoadingError();
            }
        },
    });
}

function wcbeMetaFieldsLoad() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_meta_fields_content",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            jQuery("#wcbe-meta-fields-loading").hide();
            if (response.success) {
                if (response.meta_fields != "") {
                    jQuery(".wcbe-meta-fields-empty-text").hide();
                    jQuery(".wcbe-meta-fields-items").html(response.meta_fields);
                } else {
                    jQuery(".wcbe-meta-fields-empty-text").show();
                }

                if (response.acf_fields != "" && jQuery("#wcbe-add-meta-fields-acf").length) {
                    jQuery("#wcbe-add-meta-fields-acf").html(response.acf_fields);
                }
            }
        },
        error: function () {
            jQuery("#wcbe-meta-fields-loading").hide();
        },
    });
}

function wcbeColumnManagerLoad() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_column_manager_content",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                jQuery('[data-target="#wcbe-float-side-modal-column-manager"]').attr("data-loaded", "true");
                jQuery(".wcbe-column-manager-items table tbody").html(response.profiles);
                jQuery('.wcbe-column-manager-available-fields[data-action="new"] ul').html(response.columns);
                jQuery('.wcbe-column-manager-available-fields[data-action="edit"] ul').html(response.columns);
            }
        },
        error: function () { },
    });
}

function wcbeFilterProfileLoad() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_filter_profile_content",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                jQuery('[data-target="#wcbe-float-side-modal-filter-profiles"]').attr("data-loaded", "true");
                jQuery(".wcbe-filter-profiles-items table tbody").html(response.profiles);
            }
        },
        error: function () { },
    });
}

function wcbeColumnProfileLoad() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_column_profile_content",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                columnPresetsFields = response.column_presets_fields;
                defaultPresets = response.default_presets;
                jQuery('[data-target="#wcbe-float-side-modal-column-profiles"]').attr("data-loaded", "true");
                jQuery(".wcbe-column-profiles-fields").html(response.columns);
                jQuery("#wcbe-column-profiles-choose")
                    .html(response.profiles)
                    .ready(function () {
                        jQuery("#wcbe-column-profiles-choose").trigger("change");
                    });
            }
        },
        error: function () { },
    });
}

function wcbeHistoryChangePage(page = 1, filters = null) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_history_change_page",
            nonce: WCBE_DATA.ajax_nonce,
            page: page,
            filters: filters,
        },
        success: function (response) {
            if (response.success) {
                wcbeLoadingSuccess();
                if (response.history_items) {
                    jQuery(".wcbe-history-items tbody").html(response.history_items);
                    jQuery(".wcbe-history-pagination-container").html(response.history_pagination);
                } else {
                    jQuery(".wcbe-history-items tbody").html("<td colspan='4'><span>" + wcbeTranslate.notFound + "</span></td>");
                }
                jQuery(".wcbe-history-pagination-loading").hide();
            } else {
                jQuery(".wcbe-history-pagination-loading").hide();
            }
        },
        error: function () {
            jQuery(".wcbe-history-pagination-loading").hide();
        },
    });
}

function wcbeGetCurrentPage() {
    return jQuery(".wcbe-items-pagination .wcbe-top-nav-filters-paginate a.current").attr("data-index");
}

function wcbeGetDefaultFilterProfileProducts() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_default_filter_profile_products",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                wcbeFilterFormTabsInit(response.filter_data);
                wcbeSetProductsList(response);
                wcbeCheckUndoRedoStatus(response.reverted, response.history);
                setTimeout(function () {
                    let variableId = wcbeGetUrlParameter("manage_variations");
                    if (variableId) {
                        jQuery('#wcbe-items-table td[data-item-id="' + variableId + '"] button.wcbe-variation-item-btn').trigger("click");
                        window.history.pushState("", "", wcbeRemoveParamFromURL(window.location.href, "manage_variations"));
                    }
                }, 400);
            }
        },
        error: function () { },
    });
}

function setFilterValues(filterData) {
    if (filterData.show_variations == "yes") {
        jQuery(".wcbe-bulk-edit-show-variations-button").addClass("selected");
        jQuery("#wcbe-bulk-edit-show-variations").prop("checked", true);
    } else {
        jQuery(".wcbe-bulk-edit-show-variations-button").removeClass("selected");
        jQuery("#wcbe-bulk-edit-show-variations").prop("checked", false);
    }

    if (filterData.fields && Object.keys(filterData.fields).length) {
        jQuery(".wcbe-bulk-edit-status-filter-item").removeClass("active");
        jQuery.each(filterData.fields, function (key, object) {
            if (object.value instanceof Object) {
                if (object.operator) {
                    jQuery('#wcbe-float-side-modal-filter .wcbe-form-group[data-name="' + object.name + '"]')
                        .find('[data-field="operator"]')
                        .val(object.operator)
                        .change();
                }
                if (object.value.length) {
                    let selectElement = jQuery('#wcbe-float-side-modal-filter .wcbe-form-group[data-name="' + object.name + '"]').find('[data-field="value"]');
                    jQuery.each(object.value, function (i, valueItem) {
                        if (WCBE_DATA.filter_option_values && WCBE_DATA.filter_option_values[object.name] && WCBE_DATA.filter_option_values[object.name][valueItem]) {
                            selectElement.append('<option value="' + valueItem + '" selected>' + WCBE_DATA.filter_option_values[object.name][valueItem] + "</option>");
                        }
                    });
                    selectElement.change();
                }
                if (object.value.from) {
                    jQuery('#wcbe-float-side-modal-filter .wcbe-form-group[data-name="' + object.name + '"]')
                        .find('[data-field="from"]')
                        .val(object.value.from)
                        .change();
                }
                if (object.value.to) {
                    jQuery('#wcbe-float-side-modal-filter .wcbe-form-group[data-name="' + object.name + '"]')
                        .find('[data-field="to"]')
                        .val(object.value.to);
                }
            } else {
                let element = jQuery('#wcbe-float-side-modal-filter .wcbe-form-group[data-name="' + object.name + '"]').find('[data-field="value"]');
                if (element.hasClass("wcbe-filter-form-select2-option-values")) {
                    if (WCBE_DATA.filter_option_values && WCBE_DATA.filter_option_values[object.name] && WCBE_DATA.filter_option_values[object.name][object.value]) {
                        element.html('<option value="' + object.value + '" selected>' + WCBE_DATA.filter_option_values[object.name][object.value] + "</option>").change();
                    }
                } else {
                    element.val(object.value).change();
                }
            }
        });
        wcbeCheckFilterFormChanges();
        wcbeCheckResetFilterButton();
        // wcbeFilterFormCheckAttributes();
    }
}

function checkedCurrentCategory(id, categoryIds) {
    categoryIds.forEach(function (value) {
        jQuery(id + ' input[value="' + value + '"]').prop("checked", "checked");
    });
}

function wcbeSaveFilterPreset(data, presetName) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_save_filter_preset",
            nonce: WCBE_DATA.ajax_nonce,
            filter_data: data,
            preset_name: presetName,
            option_values: wcbeGetFilterFormOptionValues(),
        },
        success: function (response) {
            jQuery("#wcbe-filter-form-save-preset").prop("disabled", false);
            if (response.success) {
                wcbeLoadingSuccess();
                jQuery("#wcbe-float-side-modal-filter-profiles").find("tbody").append(response.new_item);
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            jQuery("#wcbe-filter-form-save-preset").prop("disabled", false);
            wcbeLoadingError();
        },
    });
}

function wcbeResetBulkEditForm() {
    jQuery("#wcbe-float-side-modal-bulk-edit input, #wcbe-float-side-modal-bulk-new-products input").val("").change();
    jQuery('#wcbe-float-side-modal-bulk-edit input[type="checkbox"]').prop("checked", false).change();
    jQuery(
        '#wcbe-float-side-modal-bulk-new-products select[data-field="value"] ,#wcbe-float-side-modal-bulk-edit select[data-field="value"], #wcbe-float-side-modal-bulk-edit select[data-field="operator"], #wcbe-float-side-modal-bulk-edit select[data-field="round"], #wcbe-float-side-modal-bulk-edit select[data-field="variable"]'
    )
        .prop("selectedIndex", 0)
        .change();
    jQuery("#wcbe-float-side-modal-bulk-edit .wcbe-select2, #wcbe-float-side-modal-bulk-new-products .wcbe-select2").val("").trigger("change");
    jQuery("#wcbe-float-side-modal-bulk-new-products .wcbe-select2-item, #wcbe-float-side-modal-bulk-edit .wcbe-select2-item").val("").trigger("change");
    jQuery("#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-custom-field-files, #wcbe-float-side-modal-bulk-new-products .wcbe-bulk-new-custom-field-files").html("");
    jQuery("#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-item-image-preview, #wcbe-float-side-modal-bulk-new-products .wcbe-bulk-edit-form-item-image-preview ").html("");
    jQuery("#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-item-image-preview, #wcbe-float-side-modal-bulk-new-products .wcbe-bulk-edit-form-item-image-preview")
        .closest(".wcbe-form-group")
        .find('input[data-field="value"]')
        .val("")
        .change();
    jQuery("#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-item-gallery, #wcbe-float-side-modal-bulk-new-products .wcbe-bulk-edit-form-item-gallery").html("");
    jQuery("#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-item-gallery-preview, #wcbe-float-side-modal-bulk-new-products .wcbe-bulk-edit-form-item-gallery-preview").html("");
    jQuery("nav.wcbe-tabs-navbar li a").removeClass("wcbe-tab-changed");
    setTimeout(function () {
        jQuery("#wcbe-bulk-edit-form-reset, #wcbe-bulk-new-form-reset").prop("disabled", false);
    }, 1000);
}

function wcbeGetProSearchData() {
    let data = {
        search_type: "pro_search",
        show_variations: jQuery("#wcbe-bulk-edit-show-variations").prop("checked") === true ? "yes" : "no",
        fields: {},
    };

    jQuery('#wcbe-float-side-modal-filter .wcbe-form-group [data-field="value"], #wcbe-float-side-modal-filter .wcbe-form-group [data-field="from"]').each(function () {
        if ((jQuery(this).val() != "" && jQuery(this).val() != null) || jQuery(this).attr("data-field") == "from") {
            let fromGroupElement = jQuery(this).closest(".wcbe-form-group");
            let value;

            if (jQuery(this).attr("data-field") == "value") {
                value = jQuery(this).val();
            }

            if (jQuery(this).attr("data-field") == "from") {
                let toField = fromGroupElement.find('[data-field="to"]');
                if (toField.val() != "" || jQuery(this).val() != "") {
                    value = {
                        from: jQuery(this).val(),
                        to: toField.val(),
                    };
                }
            }

            if (value) {
                data["fields"][fromGroupElement.attr("data-name")] = {
                    name: fromGroupElement.attr("data-name"),
                    filter_type: fromGroupElement.attr("data-filter-type"),
                    field_type: fromGroupElement.attr("data-field-type"),
                    operator: fromGroupElement.find('[data-field="operator"]').length ? fromGroupElement.find('[data-field="operator"]').val() : "",
                    value: value,
                };
            }
        }
    });

    return data;
}

function wcbeProductEdit(productIds, productData) {
    if (jQuery.isArray(productIds) && productIds.length > 0) {
        if (WCBE_DATA.wcbe_settings.close_popup_after_applying == "yes") {
            wcbeCloseFloatSideModal();
        }
        if (WCBE_DATA.wcbe_settings.keep_filled_data_in_bulk_edit_form == "no") {
            wcbeResetBulkEditForm();
        }
        wcbeProductEditAction(productIds, productData);
    } else {
        swal(
            {
                title: "Are you sure?",
                text: wcbeTranslate.areYouSureForEditAllFilteredProducts,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    if (WCBE_DATA.wcbe_settings.close_popup_after_applying == "yes") {
                        wcbeCloseFloatSideModal();
                    }
                    if (WCBE_DATA.wcbe_settings.keep_filled_data_in_bulk_edit_form == "yes") {
                        wcbeResetBulkEditForm();
                    }
                    wcbeProductEditAction("all_filtered", productData);
                } else {
                    jQuery(".wcbe-bulk-edit-form-do-bulk-edit").prop("disabled", false);
                }
            }
        );
    }
}

function wcbeProductEditAction(productIds, productData) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_product_edit",
            nonce: WCBE_DATA.ajax_nonce,
            product_ids: productIds,
            product_data: productData,
            current_page: wcbeGetCurrentPage(),
        },
        success: function (response) {
            jQuery(".wcbe-bulk-edit-form-do-bulk-edit").prop("disabled", false);
            if (response.success) {
                if (response.is_processing === true) {
                    wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                    wcbeIsProcessing(response.product_ids);
                } else {
                    wcbeReloadRows(response.products, response.product_statuses);
                    wcbeSetStatusFilter(response.status_filters);
                    wcbeCheckUndoRedoStatus(response.reverted, response.history_items);
                    jQuery(".wcbe-history-items tbody").html(response.history_items);
                    jQuery(".wcbe-history-pagination-container").html(response.history_pagination);
                    wcbeReInitDatePicker();
                    wcbeReInitColorPicker();
                    let wcbeTextEditors = jQuery('input[name="wcbe-editors[]"]');
                    if (wcbeTextEditors.length > 0) {
                        wcbeTextEditors.each(function () {
                            tinymce.execCommand("mceRemoveEditor", false, jQuery(this).val());
                            tinymce.execCommand("mceAddEditor", false, jQuery(this).val());
                        });
                    }
                    wcbeLoadingSuccess();
                }
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            jQuery(".wcbe-bulk-edit-form-do-bulk-edit").prop("disabled", false);
            wcbeLoadingError();
        },
    });
}

function wcbeReloadRows(products, statuses) {
    if (Object.keys(products).length != Object.keys(statuses).length) {
        wcbeReloadProducts();
    } else {
        let currentStatus = jQuery("#wcbe-filter-form-product-status").val();
        let oldEdited = jQuery("tr.wcbe-item-edited");
        oldEdited.removeClass("wcbe-item-edited");
        if (!wcbeSelectAllChecked() && !wcbeSelectVisibleChecked()) {
            oldEdited.find(".wcbe-check-item").prop("checked", false);
        }
        if (products instanceof Object && Object.keys(products).length) {
            jQuery.each(products, function (key, val) {
                if (key) {
                    if (statuses[key] === currentStatus || (!currentStatus && statuses[key] !== "trash")) {
                        jQuery("#wcbe-items-list")
                            .find('tr[data-item-id="' + key + '"]')
                            .replaceWith(val);
                        jQuery('tr[data-item-id="' + key + '"]')
                            .addClass("wcbe-item-edited")
                            .find(".wcbe-check-item")
                            .prop("checked", true);
                    } else {
                        jQuery("#wcbe-items-list")
                            .find('tr[data-item-id="' + key + '"]')
                            .remove();
                    }
                }

                if (Object.keys(statuses).length) {
                    jQuery.each(statuses, function (key, val) {
                        if (val == "trash") {
                            jQuery("#wcbe-items-list")
                                .find('tr[data-item-id="' + key + '"]')
                                .remove();
                        }
                    });
                }
            });
            wcbeCheckSelectAllStatus();
            wcbeShowSelectionTools();
        } else {
            wcbeHideSelectionTools();
        }
    }
}

function wcbeGetTaxonomyParentSelectBox(taxonomy) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_taxonomy_parent_select_box",
            nonce: WCBE_DATA.ajax_nonce,
            taxonomy: taxonomy,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-new-product-taxonomy-parent").html(response.options);
            }
        },
        error: function () { },
    });
}

function getAttributeValues(name, target) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_attribute_values",
            nonce: WCBE_DATA.ajax_nonce,
            attribute_name: name,
        },
        success: function (response) {
            if (response.success) {
                jQuery(target).append(response.attribute_item);
                jQuery(".wcbe-select2-ajax").select2();
            } else {
            }
        },
        error: function () { },
    });
}

function wcbeGetProductBadges(productId) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_badge_ids",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-modal-product-badge-items").val(response.badges).change();
            }
        },
        error: function () { },
    });
}

function wcbeGetProductIthemelandBadge(productId) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_ithemeland_badge",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
        },
        success: function (response) {
            if (response.success) {
                if (Object.keys(response.badge_fields).length > 0) {
                    jQuery.each(response.badge_fields, function (key) {
                        switch (key) {
                            case "_unique_label_exclude":
                                jQuery('[name="' + key + '"]').prop("checked", response.badge_fields[key] === "yes");
                                break;
                            case "_unique_label_shape":
                                jQuery('[name="' + key + '"][value="' + response.badge_fields[key] + '"]').trigger("click");
                                break;
                            case "_unique_label_rotation_x":
                            case "_unique_label_rotation_y":
                            case "_unique_label_rotation_z":
                                jQuery('[name="' + key + '"]')
                                    .val(response.badge_fields[key])
                                    .change()
                                    .closest("span")
                                    .find(".range-slider__value")
                                    .text(response.badge_fields[key]);
                                break;
                            case "_unique_label_opacity":
                                jQuery('[name="' + key + '"]')
                                    .val(response.badge_fields[key])
                                    .change()
                                    .closest("p")
                                    .find(".range-slider__value")
                                    .text(response.badge_fields[key]);
                                break;
                            default:
                                jQuery('[name="' + key + '"]')
                                    .val(response.badge_fields[key])
                                    .change();
                        }
                    });
                }
            }
        },
        error: function () { },
    });
}

function wcbeGetYikesCustomProductTabs(productId) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_yikes_custom_product_tabs",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-modal-yikes-custom-tabs").html(response.tabs_html);
                setTimeout(function () {
                    if (response.text_editor_ids) {
                        jQuery.each(response.text_editor_ids, function (key) {
                            tinymce.remove("#" + response.text_editor_ids[key]);
                            tinymce.execCommand("mceAddEditor", true, response.text_editor_ids[key]);
                        });
                    }
                }, 100);

                setTimeout(function () {
                    jQuery(".wcbe-yikes-override-tab").trigger("change");
                }, 250);
            }
        },
        error: function () { },
    });
}

function wcbeAddYikesSavedTab(tabId) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_add_yikes_saved_tab",
            nonce: WCBE_DATA.ajax_nonce,
            tab_id: tabId,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-modal-yikes-custom-tabs").append(response.tab_html);
                setTimeout(function () {
                    if (response.text_editor_id) {
                        tinymce.remove("#" + response.text_editor_id);
                        tinymce.execCommand("mceAddEditor", true, response.text_editor_id);
                    }
                }, 100);
                setTimeout(function () {
                    jQuery(".wcbe-yikes-override-tab").trigger("change");
                }, 250);
            }
        },
        error: function () { },
    });
}

function wcbeGetItWcRolePrices(productId) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_it_wc_role_prices",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-modal-it-wc-dynamic-pricing")
                    .find('input[data-type="value"]')
                    .each(function () {
                        if (response.prices[jQuery(this).attr("data-name")]) {
                            let amount = response.prices[jQuery(this).attr("data-name")].price ? response.prices[jQuery(this).attr("data-name")].price : response.prices[jQuery(this).attr("data-name")].amount;
                            jQuery(this).val(amount).change();
                        } else {
                            jQuery(this).val("").change();
                        }
                    });
            }
        },
        error: function () { },
    });
}

function wcbeGetItWcDynamicPricingSelectedRoles(productId, field) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_it_wc_dynamic_pricing_selected_roles",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
            field: field,
        },
        success: function (response) {
            if (response.success) {
                if (jQuery.isArray(response.roles) && response.roles.length > 0) {
                    jQuery("#wcbe-modal-it-wc-dynamic-pricing-select-roles #wcbe-user-roles").val(response.roles).change();
                } else {
                    jQuery("#wcbe-modal-it-wc-dynamic-pricing-select-roles #wcbe-user-roles").val("").change();
                }
            }
        },
        error: function () { },
    });
}

function wcbeGetItWcDynamicPricingAllFields(productId) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_it_wc_dynamic_pricing_all_fields",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
        },
        success: function (response) {
            if (response.success) {
                let element = jQuery("#wcbe-modal-it-wc-dynamic-pricing-all-fields");

                if (response.it_product_disable_discount == "yes") {
                    element.find("input#wcbe-it-wc-dynamic-pricing-disable-discount").prop("checked", true);
                } else {
                    element.find("input#wcbe-it-wc-dynamic-pricing-disable-discount").prop("checked", false);
                }

                if (response.it_product_hide_price_unregistered == "yes") {
                    element.find("input#wcbe-it-wc-dynamic-pricing-hide-price-unregistered").prop("checked", true);
                } else {
                    element.find("input#wcbe-it-wc-dynamic-pricing-hide-price-unregistered").prop("checked", false);
                }

                if (response.it_pricing_product_price_user_role) {
                    element.find("select#wcbe-select-roles-hide-price").val(response.it_pricing_product_price_user_role).change();
                }

                if (response.it_pricing_product_add_to_cart_user_role) {
                    element.find("select#wcbe-select-roles-hide-add-to-cart").val(response.it_pricing_product_add_to_cart_user_role).change();
                }

                if (response.it_pricing_product_hide_user_role) {
                    element.find("select#wcbe-select-roles-hide-product").val(response.it_pricing_product_hide_user_role).change();
                }

                if (response.pricing_rules_product.price_rule && response.pricing_rules_product.price_rule instanceof Object && Object.keys(response.pricing_rules_product.price_rule).length > 0) {
                    element.find('#wcbe-it-pricing-roles input[data-type="value"]').each(function () {
                        if (response.pricing_rules_product.price_rule[jQuery(this).attr("data-name")]) {
                            let amount = response.pricing_rules_product.price_rule[jQuery(this).attr("data-name")].price
                                ? response.pricing_rules_product.price_rule[jQuery(this).attr("data-name")].price
                                : response.pricing_rules_product.price_rule[jQuery(this).attr("data-name")].amount;
                            jQuery(this).val(amount).change();
                        } else {
                            jQuery(this).val("").change();
                        }
                    });
                }
            }
        },
        error: function () { },
    });
}

function wcbeFilterFormCheckAttributes() {
    let attributes = jQuery('.wcbe-tab-content-item[data-content="filter_taxonomies"] .wcbe-form-group[data-type="attribute"]');
    if (attributes.length > 0) {
        jQuery.each(attributes, function () {
            let valueField = jQuery(this).find('select[data-field="value"]');
            if (jQuery.isArray(valueField.val()) && valueField.val().length > 0) {
                jQuery("#wcbe-bulk-edit-show-variations").prop("checked", true).change();
            }
        });
    }
}

function wcbeBulkNewTabsInit() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_bulk_new_tabs_content",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                jQuery('[data-target="#wcbe-float-side-modal-bulk-new-products"]').attr("data-tabs-loaded", "true");

                jQuery('#wcbe-float-side-modal-bulk-new-products .wcbe-tab-content-item[data-content="general"]').html(response.general);
                jQuery('#wcbe-float-side-modal-bulk-new-products .wcbe-tab-content-item[data-content="taxonomies"]').html(response.taxonomies);
                jQuery('#wcbe-float-side-modal-bulk-new-products .wcbe-tab-content-item[data-content="type"]').html(response.type);
                jQuery('#wcbe-float-side-modal-bulk-new-products .wcbe-tab-content-item[data-content="stock"]').html(response.stock);
                jQuery('#wcbe-float-side-modal-bulk-new-products .wcbe-tab-content-item[data-content="pricing"]').html(response.pricing);
                jQuery('#wcbe-float-side-modal-bulk-new-products .wcbe-tab-content-item[data-content="shipping"]').html(response.shipping);
                jQuery('#wcbe-float-side-modal-bulk-new-products .wcbe-tab-content-item[data-content="compatibles"]').html(response.compatibles);
                jQuery('#wcbe-float-side-modal-bulk-new-products .wcbe-tab-content-item[data-content="custom_fields"]')
                    .html(response.custom_fields)
                    .ready(function () {
                        wcbeSelect2TaxonomiesInit();
                        wcbeReInitDatePicker();
                        wcbeSelect2UsersInit();
                        wcbeSelect2ProductsInit();
                        wcbeSelect2Init();
                    });
            }
        },
        error: function () { },
    });
}

function wcbeBulkEditTabsInit() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_bulk_edit_tabs_content",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                jQuery('[data-target="#wcbe-float-side-modal-bulk-edit"]').attr("data-tabs-loaded", "true");

                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="general"]').html(response.general);
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="taxonomies"]').html(response.taxonomies);
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="type"]').html(response.type);
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="stock"]').html(response.stock);
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="pricing"]').html(response.pricing);
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="shipping"]').html(response.shipping);
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="compatibles"]').html(response.compatibles);
                jQuery('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="custom_fields"]')
                    .html(response.custom_fields)
                    .ready(function () {
                        wcbeSelect2TaxonomiesInit();
                        wcbeReInitDatePicker();
                        wcbeSelect2UsersInit();
                        wcbeSelect2ProductsInit();
                        wcbeSelect2Init();
                    });
            }
        },
        error: function () { },
    });
}

function wcbeFilterFormTabsInit(filterData = {}) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_filter_form_tabs_content",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                jQuery('[data-target="#wcbe-float-side-modal-filter"]').attr("data-tabs-loaded", "true");
                jQuery('#wcbe-float-side-modal-filter .wcbe-tab-content-item[data-content="filter_general"]').html(response.general);
                jQuery('#wcbe-float-side-modal-filter .wcbe-tab-content-item[data-content="filter_taxonomies"]').html(response.taxonomies);
                jQuery('#wcbe-float-side-modal-filter .wcbe-tab-content-item[data-content="filter_type"]').html(response.type);
                jQuery('#wcbe-float-side-modal-filter .wcbe-tab-content-item[data-content="filter_stock"]').html(response.stock);
                jQuery('#wcbe-float-side-modal-filter .wcbe-tab-content-item[data-content="filter_pricing"]').html(response.pricing);
                jQuery('#wcbe-float-side-modal-filter .wcbe-tab-content-item[data-content="filter_shipping"]').html(response.shipping);
                jQuery('#wcbe-float-side-modal-filter .wcbe-tab-content-item[data-content="filter_compatibles"]').html(response.compatibles);
                jQuery('#wcbe-float-side-modal-filter .wcbe-tab-content-item[data-content="filter_custom_fields"]')
                    .html(response.custom_fields)
                    .ready(function () {
                        wcbeSelect2TaxonomiesInit();
                        wcbeReInitDatePicker();
                        wcbeSelect2UsersInit();
                        wcbeSelect2ProductsInit();
                        wcbeSelect2Init();
                        setFilterValues(filterData);
                    });
            }
        },
        error: function () { },
    });
}

function wcbeSelect2TaxonomiesInit() {
    if (jQuery(".wcbe-select2-taxonomies").length) {
        jQuery(".wcbe-select2-taxonomies").each(function () {
            let taxonomy = jQuery(this).closest(".wcbe-form-group").attr("data-name");
            let output = jQuery(this).attr("data-output") ? jQuery(this).attr("data-output") : "term_id";
            jQuery(this).select2({
                ajax: {
                    type: "post",
                    delay: 800,
                    url: WCBE_DATA.ajax_url,
                    dataType: "json",
                    data: function (params) {
                        return {
                            action: "wcbe_get_taxonomy_terms",
                            nonce: WCBE_DATA.ajax_nonce,
                            taxonomy: taxonomy,
                            output: output,
                            search: params.term,
                        };
                    },
                },
                placeholder: "Name ...",
                minimumInputLength: 2,
            });
        });
    }
}

function wcbeSelect2UsersInit() {
    let userQuery;
    jQuery(".wcbe-select2-users").select2({
        ajax: {
            type: "post",
            delay: 800,
            url: WCBE_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                userQuery = {
                    action: "wcbe_get_users",
                    nonce: WCBE_DATA.ajax_nonce,
                    search: params.term,
                };
                return userQuery;
            },
        },
        placeholder: "Username ...",
        minimumInputLength: 3,
    });
}

function wcbeSelect2ProductsInit() {
    let query;
    jQuery(".wcbe-get-products-ajax").select2({
        ajax: {
            type: "post",
            delay: 800,
            url: WCBE_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                query = {
                    action: "wcbe_get_products_name",
                    nonce: WCBE_DATA.ajax_nonce,
                    search: params.term,
                };
                return query;
            },
        },
        placeholder: wcbeTranslate.enterProductName,
        minimumInputLength: 3,
    });
}

function wcbeLoadMoreVariations(productId, page) {
    let tdElement = jQuery('.wcbe-products-table-load-more-variations[data-variable-id="' + productId + '"]').closest("td");
    let button = tdElement.find(".wcbe-products-table-load-more-variations");
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_more_variations",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
            page: page,
        },
        success: function (response) {
            tdElement.find(".wcbe-products-table-load-more-variations-loading").hide();
            if (response.success && response.rows) {
                tdElement
                    .closest("tr")
                    .before(response.rows)
                    .ready(function () {
                        tdElement.find(".wcbe-products-table-load-more-variations").attr("data-page", parseInt(page) + 1);
                        if (parseInt(button.attr("data-page")) > parseInt(button.attr("data-max-page"))) {
                            tdElement.closest("tr").remove();
                        }
                    });
            }
        },
        error: function () {
            tdElement.find(".wcbe-products-table-load-more-variations-loading").hide();
        },
    });
}

function wcbeGetProductAuthor(productId) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_author",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
        },
        success: function (response) {
            jQuery(".wcbe-modal-select-author-loading").hide();
            jQuery("#wcbe-modal-select-author-input").html('<option value="' + response.author_id + '" selected>' + response.author_name + "</option>");
        },
        error: function () {
            jQuery(".wcbe-modal-select-author-loading").hide();
        },
    });
}

function wcbeSelect2Init() {
    if (jQuery.fn.select2) {
        let select2Element = jQuery(".wcbe-select2");
        if (select2Element.length) {
            select2Element.select2({
                placeholder: "Select ...",
            });
        }
    }
}

function wcbeGetProductTaxonomyTerms(productId, taxonomy) {
    jQuery(".wcbe-modal-product-taxonomy-terms-list").html("");
    jQuery(".wcbe-modal-product-taxonomy-loading").show();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_taxonomy_terms",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
            taxonomy: taxonomy,
        },
        success: function (response) {
            if (response.success) {
                jQuery(".wcbe-modal-product-taxonomy-terms-list")
                    .html(response.terms)
                    .ready(function () {
                        wcbeFixModalHeight(jQuery("#wcbe-modal-product-taxonomy"));
                    });
            } else {
                jQuery(".wcbe-modal-product-taxonomy-terms-list").html("");
            }
            jQuery(".wcbe-modal-product-taxonomy-loading").hide();
        },
        error: function () {
            jQuery(".wcbe-modal-product-taxonomy-loading").hide();
        },
    });
}

function wcbeGetAcfTaxonomyTerms(productId, fieldName) {
    jQuery(".wcbe-modal-acf-taxonomy-multi-select-value").html("");
    jQuery(".wcbe-modal-acf-taxonomy-loading").show();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_acf_taxonomy_terms",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
            field_name: fieldName,
        },
        success: function (response) {
            if (response.success) {
                jQuery(".wcbe-modal-acf-taxonomy-multi-select-value").html(response.terms).change();
            } else {
                jQuery(".wcbe-modal-acf-taxonomy-multi-select-value").html("");
            }
            jQuery(".wcbe-modal-acf-taxonomy-loading").hide();
        },
        error: function () {
            jQuery(".wcbe-modal-acf-taxonomy-loading").hide();
        },
    });
}

function wcbeGetProductAttributeTerms(productId, attribute) {
    jQuery(".wcbe-modal-product-attribute-terms-list").html("");
    jQuery(".wcbe-modal-product-attribute-loading").show();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_attribute_terms",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productId,
            attribute: attribute,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-modal-product-attribute .wcbe-product-attribute-checkboxes .is-visible").prop("checked", response.visible).change();
                jQuery("#wcbe-modal-product-attribute .wcbe-product-attribute-checkboxes .is-visible-prev").val(response.visible ? "yes" : "no");
                jQuery("#wcbe-modal-product-attribute .wcbe-product-attribute-checkboxes .is-variation").prop("checked", response.variation).change();
                jQuery("#wcbe-modal-product-attribute .wcbe-product-attribute-checkboxes .is-variation-prev").val(response.variation ? "yes" : "no");

                jQuery(".wcbe-modal-product-attribute-terms-list")
                    .html(response.terms)
                    .ready(function () {
                        wcbeFixModalHeight(jQuery("#wcbe-modal-product-attribute"));
                    });
            } else {
                jQuery(".wcbe-modal-product-attribute-terms-list").html("");
            }
            jQuery(".wcbe-modal-product-attribute-loading").hide();
        },
        error: function () {
            jQuery(".wcbe-modal-product-attribute-loading").hide();
        },
    });
}

function wcbeIsProcessing(productIds = []) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_is_processing",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.crashed === true) {
                wcbeLoadingProcessingError("Error !");
                wcbeBackgroundProcessClearCompleteMessage();
                wcbeHistoryFilter(null, false);
            } else if (response.is_processing === false) {
                wcbeBackgroundProcessClearCompleteMessage();
                if (jQuery("#wcbe-float-side-modal-variations-bulk-edit:visible").length) {
                    jQuery('.wcbe-tab-item[data-content="add-variations"]').trigger("click");
                    let ids = [];
                    jQuery("input.wcbe-check-item:checkbox:checked").each(function () {
                        if (jQuery(this).attr("data-item-type") !== "variation") {
                            ids.push(jQuery(this).val());
                        }
                    });
                    wcbeReloadTable(ids);
                } else {
                    wcbeReloadTable(productIds);
                }

                setTimeout(function () {
                    wcbeCloseModal();
                    wcbeLoadingProcessingSuccess("Your changes have been applied");
                    if (jQuery("#wcbe-float-side-modal-variations-bulk-edit:visible").length) {
                        jQuery(".wcbe-variations-reload-table").trigger("click");
                    }
                }, 10);
            } else {
                if (!jQuery(".wcbe-processing-loading:visible").length) {
                    wcbeLoadingProcessingStart(
                        WCBE_DATA.background_process.loading_messages.processing,
                        {
                            total: response.total_tasks,
                            completed: response.completed_tasks,
                        },
                        response.remaining_time
                    );
                } else {
                    if (response.total_tasks && response.completed_tasks) {
                        jQuery('.wcbe-processing-loading span[data-type="tasks"]').show();
                        jQuery('.wcbe-processing-loading span[data-type="tasks"]').find('[data-type="total"]').text(response.total_tasks);
                        jQuery('.wcbe-processing-loading span[data-type="tasks"]')
                            .find('[data-type="completed"]')
                            .text(response.completed_tasks > 0 ? "+" + response.completed_tasks : response.completed_tasks);
                    }

                    if (response.remaining_time) {
                        jQuery("#wcbe-processing-loading").find('[data-type="time_remaining"]').find('[data-type="time"]').text(response.remaining_time);
                        jQuery("#wcbe-processing-loading").find('[data-type="time_remaining"]').show();
                    }
                }

                setTimeout(function () {
                    if (!response.is_force_stopped) {
                        wcbeIsProcessing(productIds);
                    }
                }, 3000);
            }
        },
        error: function () { },
    });
}

function wcbeBackgroundProcessClearCompleteMessage() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_background_process_clear_complete_message",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function () {
            //
        },
        error: function () {
            //
        },
    });
}

function wcbeStopProcessCheck() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_is_processing",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.is_processing === false) {
                wcbeBackgroundProcessClearCompleteMessage();
                wcbeReloadTable();
                setTimeout(function () {
                    wcbeLoadingProcessingSuccess("Stopped");
                    if (response.total_tasks && response.completed_tasks) {
                        jQuery('.wcbe-processing-loading span[data-type="tasks"]').show();
                        jQuery('.wcbe-processing-loading span[data-type="tasks"]').find('[data-type="total"]').text(response.total_tasks);
                        jQuery('.wcbe-processing-loading span[data-type="tasks"]').find('[data-type="completed"]').text(response.completed_tasks);
                    }
                    wcbeHistoryFilter(null, false);
                }, 500);
            } else {
                setTimeout(function () {
                    wcbeStopProcessCheck();
                }, 3000);
            }
        },
        error: function () { },
    });
}

function wcbeBackgroundProcessClearTasksCount() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_background_process_clear_tasks_count",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function () { },
        error: function () { },
    });
}

function wcbeBackgroundProcessingCheck() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_is_processing",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.is_processing === true) {
                if (response.is_force_stopped) {
                    wcbeStopProcessCheck();
                    wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.stopping, false);
                } else {
                    wcbeIsProcessing();
                    wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: response.total_tasks, completed: response.completed_tasks });
                }
            }
            if (response.complete_message && response.complete_message.message) {
                wcbeLoadingProcessingComplete(response.complete_message.message, response.complete_message.icon);
                wcbeBackgroundProcessClearCompleteMessage();
            }
        },
        error: function () { },
    });
}

function wcbeBackgroundProcessForceStop() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_background_process_force_stop",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success === true) {
                wcbeStopProcessCheck();
            }
        },
        error: function () { },
    });
}

function wcbeGetProductVariations(productID) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_variations",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productID,
        },
        success: function (response) {
            if (response.success) {
                jQuery(".wcbe-variation-bulk-edit-current-items").html(response.variations);
                jQuery("#wcbe-variation-bulk-edit-attributes-added").html(response.attributes);
                jQuery("#wcbe-variation-bulk-edit-attributes").val(response.selected_items).change();
                jQuery("#wcbe-variation-single-delete-items").html(response.variations_single_delete);
                jQuery(".wcbe-variation-bulk-edit-individual-items").html(response.individual);
                jQuery("#wcbe-variation-bulk-edit-do-bulk-variations").prop("disabled", false);
                jQuery("#wcbe-variation-bulk-edit-manual-add").prop("disabled", false);
                jQuery("#wcbe-variation-bulk-edit-generate").prop("disabled", false);
                jQuery(".wcbe-select2-ajax").select2();
            } else {
                jQuery(".wcbe-variation-bulk-edit-current-items").html("");
                jQuery("#wcbe-variation-bulk-edit-attributes-added").html("");
                jQuery("#wcbe-variation-bulk-edit-attributes").val("").change();
                jQuery("#wcbe-variation-single-delete-items").html("");
                jQuery(".wcbe-variation-bulk-edit-individual-items").html("");
                jQuery("#wcbe-variation-bulk-edit-manual-add").attr("disabled", "disabled");
                jQuery("#wcbe-variation-bulk-edit-generate").attr("disabled", "disabled");
                jQuery("#wcbe-variation-bulk-edit-do-bulk-variations").attr("disabled", "disabled");
            }
            jQuery(".wcbe-variation-bulk-edit-loading").hide();
        },
        error: function () {
            jQuery(".wcbe-variation-bulk-edit-current-items").html("");
            jQuery("#wcbe-variation-bulk-edit-attributes-added").html("");
            jQuery("#wcbe-variation-bulk-edit-attributes").val("").change();
            jQuery("#wcbe-variation-single-delete-items").html("");
            jQuery(".wcbe-variation-bulk-edit-individual-items").html("");
            jQuery("#wcbe-variation-bulk-edit-manual-add").attr("disabled", "disabled");
            jQuery("#wcbe-variation-bulk-edit-generate").attr("disabled", "disabled");
            jQuery("#wcbe-variation-bulk-edit-do-bulk-variations").attr("disabled", "disabled");
            jQuery(".wcbe-variation-bulk-edit-loading").hide();
        },
    });
}

function wcbeCheckShowVariations() {
    if (jQuery("#wcbe-bulk-edit-show-variations").prop("checked") === true) {
        jQuery('tr[data-item-type="variation"]').show();
        wcbeShowVariationSelectionTools();
    } else {
        jQuery('tr[data-item-type="variation"]').hide();
        wcbeHideVariationSelectionTools();
    }
}

function wcbeSetProductsVariations(productIDs, attributes, variations, default_variation) {
    wcbeLoadingStart();

    if (WCBE_DATA.wcbe_settings.close_popup_after_applying == "yes") {
        wcbeCloseFloatSideModal();
    }

    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_set_products_variations",
            nonce: WCBE_DATA.ajax_nonce,
            product_ids: productIDs,
            attributes: attributes,
            variations: variations,
            default_variation: default_variation,
        },
        success: function (response) {
            if (response.success) {
                wcbeReloadProducts(productIDs);
                wcbeCheckUndoRedoStatus(response.reverted, response.history_items);
                jQuery(".wcbe-history-items tbody").html(response.history_items);
                jQuery(".wcbe-history-pagination-container").html(response.history_pagination);
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeDeleteProductsVariations(ProductIds, deleteType, variations) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_delete_products_variations",
            nonce: WCBE_DATA.ajax_nonce,
            product_ids: ProductIds,
            delete_type: deleteType,
            variations: variations,
        },
        success: function (response) {
            if (response.success) {
                wcbeReloadProducts(ProductIds, wcbeGetCurrentPage());
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function getProductVariationsForAttach(productID, attribute, attributeItem) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_product_variations_for_attach",
            nonce: WCBE_DATA.ajax_nonce,
            product_id: productID,
            attribute: attribute,
            attribute_item: attributeItem,
        },
        success: function (response) {
            if (response.success && response.variations) {
                jQuery("#wcbe-variations-attaching-product-variations").html(response.variations);
                jQuery("#wcbe-variation-attaching-start-attaching").prop("disabled", false);
            } else {
                jQuery("#wcbe-variation-attaching-start-attaching").attr("disabled", "disabled");
                jQuery("#wcbe-variations-attaching-product-variations").html('<span class="wcbe-alert wcbe-alert-danger">' + wcbeTranslate.productHasNoVariations + "</span>");
            }
        },
        error: function () {
            jQuery("#wcbe-variation-attaching-start-attaching").attr("disabled", "disabled");
            jQuery("#wcbe-variations-attaching-product-variations").html("");
        },
    });
}

function getAttributeValuesForAttach(name) {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_get_attribute_values_for_attach",
            nonce: WCBE_DATA.ajax_nonce,
            attribute_name: name,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-variation-attaching-attribute-items").html(
                    '<div class="wcbe-w40p wcbe-float-left"><select title="' +
                    wcbeTranslate.selectAttribute +
                    '" id="wcbe-variations-attaching-attribute-item" class="wcbe-w100p">' +
                    response.attribute_items +
                    "</select></div>"
                );
                jQuery(".wcbe-variations-attaching-variation-attribute-item").html(response.attribute_items);
            } else {
                jQuery("#wcbe-variation-attaching-attribute-items").html("");
                jQuery(".wcbe-variations-attaching-variation-attribute-item").html("");
            }
        },
        error: function () {
            jQuery("#wcbe-variation-attaching-attribute-items").html("");
            jQuery(".wcbe-variations-attaching-variation-attribute-item").html("");
        },
    });
}

function wcbeVariationAttaching(productId, attributeKey, variationId, attributeItem) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_variation_attaching",
            nonce: WCBE_DATA.ajax_nonce,
            attribute_key: attributeKey,
            variation_id: variationId,
            attribute_item: attributeItem,
        },
        success: function (response) {
            if (response.success) {
                wcbeReloadProducts([productId]);
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeGetAllCombinations(attributes_arr) {
    var combinations = [],
        args = attributes_arr,
        max = args.length - 1;
    helper([], 0);

    function helper(arr, i) {
        for (let j = 0; j < args[i][1].length; j++) {
            let a = arr.slice(0);
            a.push([args[i][0], args[i][1][j]]);
            if (i === max) {
                combinations.push(a);
            } else {
                helper(a, i + 1);
            }
        }
    }

    return combinations;
}
