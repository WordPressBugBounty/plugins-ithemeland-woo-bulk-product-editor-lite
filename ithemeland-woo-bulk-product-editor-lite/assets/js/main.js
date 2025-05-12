jQuery(document).ready(function ($) {
    "use strict";

    // Inline edit
    $(document).on("click", 'td[data-action="inline-editable"]', function (e) {
        if ($(e.target).attr("data-type") !== "edit-mode" && $(e.target).find('[data-type="edit-mode"]').length === 0) {
            // Close All Inline Edit
            $('[data-type="edit-mode"]').each(function () {
                $(this).closest("span").html($(this).attr("data-val"));
            });

            let text = $(this).find('[data-action="inline-editable"]').text().trim();
            let fullText =
                $(this).find('[data-action="inline-editable"]').attr("data-full-text") && $(this).find('[data-action="inline-editable"]').attr("data-full-text") != ""
                    ? $(this).find('[data-action="inline-editable"]').attr("data-full-text").trim()
                    : text;

            // Open Clicked Inline Edit
            switch ($(this).attr("data-content-type")) {
                case "text":
                case "password":
                case "url":
                case "email":
                    $(this)
                        .children("span")
                        .html(
                            "<textarea data-item-id='" +
                            $(this).attr("data-item-id") +
                            "' data-field='" +
                            $(this).attr("data-field") +
                            "' data-field-type='" +
                            $(this).attr("data-field-type") +
                            "' data-type='edit-mode' data-val='" +
                            text +
                            "'>" +
                            fullText +
                            "</textarea>"
                        )
                        .children("textarea")
                        .focus()
                        .select();
                    break;
                case "numeric":
                case "regular_price":
                case "sale_price":
                    $(this)
                        .children("span")
                        .html(
                            "<input type='number' min='-1' data-item-id='" +
                            $(this).attr("data-item-id") +
                            "' data-field='" +
                            $(this).attr("data-field") +
                            "' data-field-type='" +
                            $(this).attr("data-field-type") +
                            "' data-type='edit-mode' data-val='" +
                            text +
                            "' value='" +
                            fullText.replaceAll(",", "") +
                            "'>"
                        )
                        .children("input[type=number]")
                        .focus()
                        .select();
                    break;
            }
        }
    });

    // Discard Save
    $(document).on("click", function (e) {
        if ($(e.target).attr("data-action") !== "inline-editable" && $(e.target).attr("data-type") !== "edit-mode") {
            $('[data-type="edit-mode"]').each(function () {
                $(this).closest("span").html($(this).attr("data-val"));
            });
        }
    });

    $(document).on("click", ".wcbe-reload-table", function () {
        wcbeReloadProducts();
    });

    // Save Inline Edit By Enter Key
    $(document).on("keypress", '[data-type="edit-mode"]', function (event) {
        let wcbeKeyCode = event.keyCode ? event.keyCode : event.which;
        if (wcbeKeyCode === 13) {
            let productData = [];
            let productIds;
            let tdElement = $(this).closest("td");

            if (wcbeSelectAllChecked() && $("#wcbe-bind-edit").prop("checked") === true) {
                productIds = "all_filtered";
            } else {
                if ($("#wcbe-bind-edit").prop("checked") === true) {
                    productIds = wcbeGetProductsChecked();
                } else {
                    productIds = [];
                }
                if ($.isArray(productIds)) {
                    productIds.push($(this).attr("data-item-id"));
                }
            }

            productData.push({
                name: tdElement.attr("data-name"),
                sub_name: tdElement.attr("data-sub-name") ? tdElement.attr("data-sub-name") : "",
                type: tdElement.attr("data-update-type"),
                value: $(this).val(),
                operation: "inline_edit",
            });

            $(this).closest("span").html($(this).val());
            wcbeProductEditAction(productIds, productData);
        }
    });

    $(document).on("input", "#wcbe-meta-fields-manual_key_name", function () {
        let containerElement = $(this).closest(".wcbe-meta-fields-manual-field");
        let errorMessageElement = containerElement.find(".wcbe-add-meta-field-message");
        if ($(this).val() != "") {
            if ($.inArray($(this).val().toLowerCase(), WCBE_DATA.reserved_field_keys) === -1 && !$('input.wcbe_meta_field_key_input[value="' + $(this).val().toLowerCase() + '"]').length) {
                $("#wcbe-add-meta-field-manual").prop("disabled", false);
                containerElement.removeClass("wcbe-add-meta-field-name-error");
                errorMessageElement.text("");
            } else {
                $("#wcbe-add-meta-field-manual").prop("disabled", true);
                containerElement.addClass("wcbe-add-meta-field-name-error");
                errorMessageElement.text("This name exists");
            }
        } else {
            $("#wcbe-add-meta-field-manual").prop("disabled", true);
            containerElement.removeClass("wcbe-add-meta-field-name-error");
            errorMessageElement.text("");
        }
    });

    // fetch product data by click to bulk edit button
    $(document).on("click", '[data-target="#wcbe-float-side-modal-bulk-edit"]', function () {
        if (WCBE_DATA.wcbe_settings.keep_filled_data_in_bulk_edit_form === "yes") {
            let productID = $("input.wcbe-check-item:visible:checkbox:checked");
            if (productID.length === 1) {
                wcbeGetProductData(productID.val());
            } else {
                wcbeResetBulkEditForm();
            }
        }
    });

    $(document).on("click", ".wcbe-inline-edit-color-action", function () {
        $(this).closest("td").find("input.wcbe-inline-edit-action").trigger("change");
    });

    $(document).on("change", ".wcbe-inline-edit-action", function (e) {
        let $this = $(this);
        setTimeout(function () {
            if ($("div.xdsoft_datetimepicker:visible").length > 0) {
                e.preventDefault();
                return false;
            }

            if ($this.hasClass("wcbe-datepicker") || $this.hasClass("wcbe-timepicker") || $this.hasClass("wcbe-datetimepicker")) {
                if ($this.attr("data-val") == $this.val()) {
                    e.preventDefault();
                    return false;
                }
            }

            let productData = [];
            let productIds;
            let tdElement = $this.closest("td");
            if ($("#wcbe-bind-edit").prop("checked") === true) {
                productIds = wcbeGetProductsChecked();
            } else {
                productIds = [];
            }
            if ($.isArray(productIds)) {
                productIds.push($this.attr("data-item-id"));
            }

            let wcbeValue;
            switch (tdElement.attr("data-content-type")) {
                case "checkbox_dual_mode":
                case "checkbox":
                    wcbeValue = $this.prop("checked") ? "yes" : "no";
                    break;
                default:
                    wcbeValue = $this.val();
                    break;
            }

            productData.push({
                name: tdElement.attr("data-name"),
                sub_name: tdElement.attr("data-sub-name") ? tdElement.attr("data-sub-name") : "",
                type: tdElement.attr("data-update-type"),
                value: wcbeValue,
                operation: "inline_edit",
            });

            wcbeProductEdit(productIds, productData);
        }, 250);
    });

    $(document).on("click", ".wcbe-inline-edit-clear-date", function () {
        let productData = [];
        let productIds;
        let tdElement = $(this).closest("td");

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        productData.push({
            name: tdElement.attr("data-name"),
            sub_name: tdElement.attr("data-sub-name") ? tdElement.attr("data-sub-name") : "",
            type: tdElement.attr("data-update-type"),
            value: "",
            operation: "inline_edit",
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", ".wcbe-modal-regular-price-apply-button", function () {
        let productId = $(this).attr("data-item-id");
        let productIds;
        let productData = [];

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push(productId);
        }

        productData.push({
            name: "regular_price",
            sub_name: "",
            type: $(this).attr("data-update-type"),
            operator: $("#wcbe-regular-price-calculator-operator").val(),
            value: $("#wcbe-regular-price-calculator-value").val(),
            operator_type: $("#wcbe-regular-price-calculator-type").val(),
            round: $("#wcbe-regular-price-calculator-round").val(),
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", ".wcbe-modal-sale-price-apply-button", function () {
        let productId = $(this).attr("data-item-id");
        let productIds;
        let productData = [];

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push(productId);
        }

        productData.push({
            name: "sale_price",
            sub_name: "",
            type: $(this).attr("data-update-type"),
            operator: $("#wcbe-sale-price-calculator-operator").val(),
            value: $("#wcbe-sale-price-calculator-value").val(),
            operator_type: $("#wcbe-sale-price-calculator-type").val(),
            round: $("#wcbe-sale-price-calculator-round").val(),
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", ".wcbe-bulk-edit-delete-duplicate-action", function () {
        let deleteType = $(this).attr("data-delete-type"); // This should be "duplatest_title"
        console.log(deleteType);
        let alertMessage = "Are you sure you want to delete duplicates?";

        let productIds = $("input.wcbe-check-item:checkbox:checked")
            .map(function () {
                return $(this).val(); // Get the value of each checked checkbox
            })
            .get(); // Convert jQuery object to a JavaScript array
        swal(
            {
                title: alertMessage,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: "Yes, delete duplicates!",
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeDeleteProduct(productIds, deleteType); // Send the delete type here
                }
            }
        );
        if (productIds.length == 1) {
            swal({
                title: "Please Select More Than One Option!",
                type: "warning",
            });
        }
    });

    $(document).on("click", ".wcbe-bulk-edit-delete-action", function () {
        let deleteType = $(this).attr("data-delete-type");
        let productIds = wcbeGetProductsChecked();

        if (!productIds.length && productIds != "all_filtered" && deleteType != "all") {
            swal({
                title: "Please select one product",
                type: "warning",
            });
            return false;
        }

        swal(
            {
                title: wcbeTranslate.areYouSure,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeLoadingStart();
                    wcbeDeleteProduct(productIds, deleteType);
                }
            }
        );
    });

    $(document).on("click", "#wcbe-bulk-edit-duplicate-start", function () {
        let $this = $(this);
        $this.prop("disabled", true);
        let count = parseInt($("#wcbe-bulk-edit-duplicate-number").val());
        let productIDs = $("input.wcbe-check-item:visible:checkbox:checked")
            .map(function () {
                if ($(this).attr("data-item-type") === "variation") {
                    swal({
                        title: wcbeTranslate.duplicateVariationsDisabled,
                        type: "warning",
                    });
                    $this.prop("disabled", false);
                    return false;
                }
                return $(this).val();
            })
            .get();

        if (!productIDs.length) {
            swal({
                title: "Please select one product",
                type: "warning",
            });
            $this.prop("disabled", false);
            return false;
        } else {
            wcbeLoadingStart();
            wcbeDuplicateProduct(productIDs, count);
        }
    });

    $(document).on("click", ".wcbe-top-nav-duplicate-button", function () {
        let productIDs = $("input.wcbe-check-item:visible:checkbox:checked")
            .map(function () {
                if ($(this).attr("data-item-type") === "variation") {
                    swal({
                        title: wcbeTranslate.duplicateVariationsDisabled,
                        type: "warning",
                    });
                } else {
                    return $(this).val();
                }
            })
            .get();

        if (!productIDs.length) {
            swal({
                title: $('input.wcbe-check-item[data-item-type="variation"]:visible:checkbox:checked') ? wcbeTranslate.duplicateVariationsDisabled : "Please select one product",
                type: "warning",
            });
            return false;
        } else {
            wcbeOpenModal("#wcbe-modal-item-duplicate");
        }
    });

    $(document).on("click", "#wcbe-bulk-new-form-do-bulk-new", function () {
        // Get The Quantity Of New Products
        let quantity = $("#wcbe-bulk-new-form-product-quantity").val();
        if (quantity < 1) {
            swal({
                title: "The 'Quantity' most be one or more!",
                type: "warning",
            });
            return false;
        }

        wcbeLoadingStart();
        
        let title = $("#wcbe-bulk-new-form-product-title").val();
        let slug = $("#wcbe-bulk-new-form-product-slug").val();
        let sku = $("#wcbe-bulk-new-form-product-sku").val();
        let description = $("#wcbe-bulk-new-form-product-description").val();
        let shortDescription = $("#wcbe-bulk-new-form-product-short-description").val();
        let purchase_note = $("#wcbe-bulk-new-form-product-purchase-note").val();
        let menu_order = $("#wcbe-bulk-new-form-product-menu-order").val();
        let sold_individually = $("#wcbe-bulk-new-form-product-sold-individually").val();
        let reviews_allowed = $("#wcbe-bulk-new-form-product-enable-reviews").val();
        let status = $("#wcbe-bulk-new-form-product-product-status").val();
        let catalog_visibility = $("#wcbe-bulk-new-form-product-catalog-visibility").val();
        let date_created = $("#wcbe-bulk-new-form-product-date-created").val();
        let author = $("#wcbe-bulk-new-form-product-author").val();
        let image_id = $(".wcbe-bulk-edit-form-item-image").val();
        let gallery_image_ids = [];
        $(".wcbe-bulk-edit-form-item-gallery input[type='hidden']").each(function () {
            let image_ids = $(this).val(); // Get the value of each hidden input
            gallery_image_ids.push(image_ids);
        });
        // Get selected taxonomies from the form
        let taxonomies = {};
        $(".wcbe-select2-taxonomies").each(function () {
            let taxonomyName = $(this).closest(".wcbe-form-group").data("name"); // Get taxonomy name
            let selectedValues = $(this).val(); // Get selected values (term IDs or slugs)

            if (selectedValues) {
                taxonomies[taxonomyName] = selectedValues;
            }
        });

        // Get selected attributes from the form
        let attributes = {};
        $(".wcbe-form-group[data-type='taxonomy']").each(function () {
            let attributeName = $(this).data("name"); // Get attribute name

            // Get selected values (handle multiple selections)
            let selectedValues = $("#wcbe-bulk-new-form-product-attr-" + attributeName).val() || [];

            let selectedName = $("#wcbe-bulk-new-form-product-attr-" + attributeName + " option:selected")
                .map(function () {
                    return $(this).text();
                })
                .get();

            // Get visibility and variation usage values
            let isVisible = $("#wcbe-bulk-new-form-product-attr-is-visible-" + attributeName).val() || "no";
            let usedForVariations = $("#wcbe-bulk-new-form-product-attr-for-variations-" + attributeName).val() || "no";

            // Store only if there are selected values
            if (selectedValues.length > 0) {
                attributes[attributeName] = {
                    name: selectedName,
                    values: selectedValues,
                    is_visible: isVisible,
                    used_for_variations: usedForVariations,
                };
            }
        });

        // Get the value regular price and round
        let regular_price = $("#wcbe-bulk-new-form-regular-price").val();
        let round_item_regular_price = $("#wcbe-bulk-new-form-regular-price-round-item").val();
        //Get the value sales price and round
        let sale_price = $("#wcbe-bulk-new-form-sale-price").val();
        let round_item_sales_price = $("#wcbe-bulk-new-form-sale-price-round-item").val();
        //Get the value sale date
        let sale_date_from = $("#wcbe-bulk-new-form-sale-date-from").val();
        let sale_date_to = $("#wcbe-bulk-new-form-sale-date-to").val();
        //Get the value of Taxes
        let tax_status = $("#wcbe-bulk-new-form-tax-status").val();
        let tax_class = $("#wcbe-bulk-new-form-tax-class").val();
        //Get the value of Shipping
        let shipping_class = $("#wcbe-bulk-new-form-shipping-class").val();
        let width = $("#wcbe-bulk-new-form-width").val();
        let height = $("#wcbe-bulk-new-form-height").val();
        let length = $("#wcbe-bulk-new-form-length").val();
        let weight = $("#wcbe-bulk-new-form-weight").val();
        //Get the value of Stock
        let manage_stock = $("#wcbe-bulk-new-form-manage-stock").val();
        let stock_status = $("#wcbe-bulk-new-form-stock-status").val();
        let stock_quantity = $("#wcbe-bulk-new-form-stock-quantity").val();
        let backorders = $("#wcbe-bulk-new-form-backorders").val();
        //Get the value of Type
        let product_type = $("#wcbe-bulk-new-form-product-type").val();
        let featured = $("#wcbe-bulk-new-form-featured").val();
        let virtual = $("#wcbe-bulk-new-form-virtual").val();
        let downloadable = $("#wcbe-bulk-new-form-downloadable").val();
        let download_limit = $("#wcbe-bulk-new-form-download-limit").val();
        let download_expiry = $("#wcbe-bulk-new-form-download-expiry").val();
        let product_url = $("#wcbe-bulk-new-form-product-url").val();
        let button_text = $("#wcbe-bulk-new-form-button-text").val();
        let upsells = $("#wcbe-bulk-new-form-upsells").val();
        let cross_sells = $("#wcbe-bulk-new-form-cross-sells").val();
        // Prepare data object to send via AJAX
        let productData = {
            action: "create_bulk_products",
            title: title,
            slug: slug,
            sku: sku,
            description: description,
            short_description: shortDescription,
            purchase_note: purchase_note,
            menu_order: menu_order,
            sold_individually: sold_individually,
            reviews_allowed: reviews_allowed,
            status: status,
            catalog_visibility: catalog_visibility,
            date_created: date_created,
            author: author,
            image_id: image_id,
            gallery_image_ids: gallery_image_ids,
            taxonomies: taxonomies,
            attributes: attributes,
            regular_price: regular_price,
            round_item_regular_price: round_item_regular_price,
            sale_price: sale_price,
            round_item_sales_price: round_item_sales_price,
            sale_date_from: sale_date_from,
            sale_date_to: sale_date_to,
            tax_status: tax_status,
            tax_class: tax_class,
            shipping_class: shipping_class,
            width: width,
            height: height,
            length: length,
            weight: weight,
            manage_stock: manage_stock,
            stock_status: stock_status,
            stock_quantity: stock_quantity,
            backorders: backorders,
            product_type: product_type,
            featured: featured,
            virtual: virtual,
            downloadable: downloadable,
            download_limit: download_limit,
            download_expiry: download_expiry,
            product_url: product_url,
            button_text: button_text,
            upsells: upsells,
            cross_sells: cross_sells,
        };

        wcbeCreateNewProduct(quantity, productData);
    });

    $(document).on("click", ".wcbe-open-uploader", function () {
        let gallery_image_ids = new Set();

        let intervalCheck = setInterval(() => {
            $(".wcbe-bulk-edit-form-item-gallery input[type='hidden']").each(function () {
                if (this.value) {
                    gallery_image_ids.add(this.value);
                }
            });

            if (gallery_image_ids.size > 1) {
                let $removeAllButton = $(".wcbe-bulk-edit-form-item-remove-all-images").show();

                $removeAllButton.off("click").on("click", function () {
                    $("#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-item-gallery, #wcbe-float-side-modal-bulk-new-products .wcbe-bulk-edit-form-item-gallery").empty();
                    $("#wcbe-float-side-modal-bulk-edit .wcbe-bulk-edit-form-item-gallery-preview, #wcbe-float-side-modal-bulk-new-products .wcbe-bulk-edit-form-item-gallery-preview").empty();
                    $removeAllButton.hide();
                });

                clearInterval(intervalCheck);
            }
        }, 500);
    });

    $(document).on("change", "#wcbe-bulk-edit-show-variations", function () {
        if ($(this).prop("checked") === true) {
            wcbeShowVariationSelectionTools();
        } else {
            wcbeHideVariationSelectionTools();
        }

        setTimeout(function () {
            wcbeReloadProducts();
        }, 30);
    });

    $(document).on("click", ".wcbe-bulk-edit-select-all-variations-button", function () {
        if ($("#wcbe-bulk-edit-select-all-variations").prop("checked") === true) {
            $(this).removeClass("selected");
            $("#wcbe-bulk-edit-select-all-variations").prop("checked", false).change();
        } else {
            $(this).addClass("selected");
            $("#wcbe-bulk-edit-select-all-variations").prop("checked", true).change();
        }
    });

    $(document).on("change", "#wcbe-bulk-edit-select-all-variations", function () {
        if ($(this).prop("checked") === true) {
            $("input.wcbe-check-item[data-item-type=variation]").prop("checked", true);
        } else {
            $("input.wcbe-check-item[data-item-type=variation]").prop("checked", false);
        }
    });

    $(document).on("click", "#wcbe-variation-bulk-edit-manual-add", function () {
        let attributes = [];
        let currents = [];
        $(".wcbe-variation-bulk-edit-current-item-name").each(function () {
            currents.push($(this).find("span").text());
        });

        $(".wcbe-variation-bulk-edit-manual-item").each(function () {
            if ($(this).val()) {
                attributes.push([$(this).attr("data-attribute-name"), $(this).val()]);
            }
        });

        let label = attributes.map(function (val) {
            return val[1];
        });

        // generate if not exist
        if (jQuery.inArray(label.join(" | "), currents) === -1) {
            $(".wcbe-variation-bulk-edit-current-items").append(
                '<div class="wcbe-variation-bulk-edit-current-item"><label class="wcbe-variation-bulk-edit-current-item-name"><input type="checkbox" name="variation_item[]" checked="checked" value="' +
                attributes.join("&&") +
                '"><span>' +
                label.join(" | ") +
                '</span></label><button type="button" class="wcbe-button wcbe-button-flat wcbe-variation-bulk-edit-current-item-sortable-btn" title="' +
                wcbeTranslate.drag +
                '"><i class=" wcbe-icon-menu1"></i></button><div class="wcbe-variation-bulk-edit-current-item-radio"><input type="radio" name="default_variation" title="' +
                wcbeTranslate.setAsDefault +
                '"></div></div>'
            );
            $("#wcbe-variation-bulk-edit-do-bulk-variations").prop("disabled", false);
        }

        wcbeSetTipsyTooltip();
    });

    $(document).on("click", "#wcbe-variation-bulk-edit-generate", function () {
        let attributes = [];
        let currents = [];
        $(".wcbe-variation-bulk-edit-current-item-name").each(function () {
            currents.push($(this).find("span").text());
        });

        $(".wcbe-variation-bulk-edit-attribute-item").each(function () {
            if ($(this).find("select").val()) {
                attributes.push([$(this).find("select").attr("data-attribute-name"), $(this).find("select").val()]);
            }
        });

        let combinations = wcbeGetAllCombinations(attributes);

        if (combinations.length > 0) {
            $(".wcbe-variation-bulk-edit-current-items").html("");
            combinations.forEach(function (value) {
                let variation = value.map(function (val) {
                    return val[1];
                });
                $(".wcbe-variation-bulk-edit-current-items").append(
                    '<div class="wcbe-variation-bulk-edit-current-item"><label class="wcbe-variation-bulk-edit-current-item-name"><input type="checkbox" name="variation_item[]" checked="checked" value="' +
                    value.join("&&") +
                    '"><span>' +
                    variation.join(" | ") +
                    '</span></label><button type="button" class="wcbe-button wcbe-button-flat wcbe-variation-bulk-edit-current-item-sortable-btn" title="' +
                    wcbeTranslate.drag +
                    '"><i class=" wcbe-icon-menu1"></i></button><div class="wcbe-variation-bulk-edit-current-item-radio"><input type="radio" name="default_variation" value="' +
                    value.join("&&") +
                    '" title="' +
                    wcbeTranslate.setAsDefault +
                    '"></div></div>'
                );
                $("#wcbe-variation-bulk-edit-do-bulk-variations").prop("disabled", false);
            });
        }
        wcbeSetTipsyTooltip();
    });

    $(document).on("click", "#wcbe-variation-bulk-edit-do-bulk-variations", function () {
        let productIds;
        let defaultVariation = $('.wcbe-variation-bulk-edit-current-item .wcbe-variation-bulk-edit-current-item-radio input:radio:checked[name="default_variation"]').val();
        let productsChecked = $("input.wcbe-check-item:visible:checkbox:checked");
        let attributes = [];
        $(".wcbe-variation-bulk-edit-attribute-item").each(function () {
            let selectItem = $(this).find("select");
            let ids = selectItem
                .select2()
                .find(":selected")
                .map(function () {
                    return $(this).attr("data-id");
                })
                .toArray();
            if (selectItem.val() != null) {
                attributes.push([$(this).find("select").attr("data-attribute-name"), ids]);
            }
        });

        let variations = [];
        $('input:checkbox:checked[name="variation_item[]"]').each(function () {
            variations.push([$(this).val(), $(this).attr("data-id")]);
        });

        if (productsChecked.length > 0) {
            let notVariable = 0;
            productsChecked.each(function () {
                if ($(this).attr("data-item-type") !== "variable") {
                    notVariable++;
                }
            });
            if (variations.length > 0) {
                productIds = productsChecked
                    .map(function () {
                        return $(this).val();
                    })
                    .get();
                if (notVariable > 0) {
                    swal(
                        {
                            title: wcbeTranslate.selectedProductsIsNotVariable,
                            type: "warning",
                            showCancelButton: true,
                            cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                            confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                            confirmButtonText: wcbeTranslate.iAmSure,
                            closeOnConfirm: true,
                        },
                        function (isConfirm) {
                            if (isConfirm === true) {
                                wcbeSetProductsVariations(productIds, attributes, variations, defaultVariation);
                            }
                        }
                    );
                } else {
                    wcbeSetProductsVariations(productIds, attributes, variations, defaultVariation);
                }
            } else {
                swal({
                    title: wcbeTranslate.variationRequired,
                    type: "warning",
                });
            }
        } else {
            swal({
                title: wcbeTranslate.productRequired,
                type: "warning",
            });
        }
    });

    $(document).on("click", "#wcbe-variation-delete-selected", function () {
        let deleteType = "single_product";
        let productIds;
        let variations;
        let productsChecked = $("input.wcbe-check-item:visible:checkbox:checked[data-item-type=variable]");
        if (productsChecked.length > 0) {
            productIds = productsChecked
                .map(function () {
                    return $(this).val();
                })
                .get();
            variations = $("#wcbe-variation-single-delete-items input:checkbox:checked")
                .map(function () {
                    return $(this).val();
                })
                .get();
            swal(
                {
                    title: wcbeTranslate.areYouSure,
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                    confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                    confirmButtonText: wcbeTranslate.iAmSure,
                    closeOnConfirm: true,
                },
                function (isConfirm) {
                    if (isConfirm === true) {
                        wcbeDeleteProductsVariations(productIds, deleteType, variations);
                    }
                }
            );
        } else {
            swal({
                title: wcbeTranslate.variableProductRequired,
                type: "warning",
            });
        }
    });

    $(document).on("click", "#wcbe-variation-delete-all", function () {
        let deleteType = "all_variations";
        let productIds;
        let productsChecked = $("input.wcbe-check-item:visible:checkbox:checked[data-item-type=variable]");
        if (productsChecked.length > 0) {
            productIds = productsChecked
                .map(function () {
                    return $(this).val();
                })
                .get();
            swal(
                {
                    title: wcbeTranslate.areYouSure,
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                    confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                    confirmButtonText: wcbeTranslate.iAmSure,
                    closeOnConfirm: true,
                },
                function (isConfirm) {
                    if (isConfirm === true) {
                        wcbeDeleteProductsVariations(productIds, deleteType, "all_variations");
                    }
                }
            );
        } else {
            swal({
                title: wcbeTranslate.variableProductRequired,
                type: "warning",
            });
        }
    });

    $(document).on("keyup", "#wcbe-variation-attaching-variable-id", function () {
        if ($(this).val() !== "") {
            $("#wcbe-variation-attaching-get-variations").prop("disabled", false);
        } else {
            $("#wcbe-variation-attaching-get-variations").attr("disabled", "disabled");
        }
    });

    $(document).on("click", "#wcbe-variation-attaching-get-variations", function () {
        getProductVariationsForAttach($("#wcbe-variation-attaching-variable-id").val(), $("#wcbe-variations-attaching-attributes").val(), $("#wcbe-variations-attaching-attribute-item").val());
    });

    $(document).on("change", "#wcbe-variations-attaching-attributes", function () {
        getAttributeValuesForAttach($(this).val());
    });

    $(document).on("click", "#wcbe-variation-attaching-start-attaching", function () {
        let productId = $("#wcbe-variation-attaching-variable-id").val();
        let attributeKey = $("#wcbe-variations-attaching-attributes").val();
        let variationId = [];
        let attributeItem = [];
        $("#wcbe-variations-attaching-product-variations .wcbe-variation-bulk-edit-current-item").map(function () {
            variationId.push($(this).find('input[type=hidden][name="variation_id[]"]').val());
            attributeItem.push($(this).find('select[name="attribute_item[]"]').val());
        });
        wcbeVariationAttaching(productId, attributeKey, variationId, attributeItem);
    });

    $(document).on("click", "#wcbe-column-profiles-save-as-new-preset", function () {
        let presetKey = $("#wcbe-column-profiles-choose").val();
        let items = $(".wcbe-column-profile-fields input:checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();
        wcbeSaveColumnProfile(presetKey, items, "save_as_new");
    });

    $(document).on("click", "#wcbe-column-profiles-update-changes", function () {
        let presetKey = $("#wcbe-column-profiles-choose").val();
        let items = $(".wcbe-column-profile-fields input:checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();
        wcbeSaveColumnProfile(presetKey, items, "update_changes");
    });

    $(document).on("click", ".wcbe-bulk-edit-filter-profile-load", function () {
        wcbeLoadFilterProfile($(this).val());

        $(".wcbe-filter-profiles-items tr").removeClass("wcbe-filter-profile-loaded");
        $(this).closest("tr").addClass("wcbe-filter-profile-loaded");

        if (WCBE_DATA.wcbe_settings.close_popup_after_applying == "yes") {
            wcbeCloseFloatSideModal();
        }
    });

    $(document).on("click", ".wcbe-bulk-edit-filter-profile-delete", function () {
        let presetKey = $(this).val();
        let item = $(this).closest("tr");
        swal(
            {
                title: wcbeTranslate.areYouSure,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeDeleteFilterProfile(presetKey);
                    if (item.hasClass("wcbe-filter-profile-loaded")) {
                        $(".wcbe-filter-profiles-items tbody tr:first-child").addClass("wcbe-filter-profile-loaded").find("input[type=radio]").prop("checked", true);
                        $("#wcbe-bulk-edit-reset-filter").trigger("click");
                    }
                    if (WCBE_DATA.wcbe_settings.close_popup_after_applying == "yes") {
                        wcbeCloseFloatSideModal();
                    }
                    item.remove();
                }
            }
        );
    });

    $(document).on("change", "input.wcbe-filter-profile-use-always-item", function () {
        wcbeFilterProfileChangeUseAlways($(this).val());
    });

    $(document).on("click", ".wcbe-filter-form-action", function (e) {
        let data = wcbeGetCurrentFilterData();
        let page;
        let action = $(this).attr("data-search-action");
        if (action === "pagination") {
            page = $(this).attr("data-index");
        } else {
            page = 1;
        }
        if (action === "quick_search" && $("#wcbe-quick-search-text").val() !== "") {
            wcbeResetFilterForm();
        }
        if (action === "pro_search") {
            wcbeResetQuickSearchForm();
        }
        wcbeProductsFilter(data, action, [], page);
    });

    $(document).on("click", "#wcbe-filter-form-reset", function () {
        $(this).prop("disabled", true);
        wcbeResetFilters();
    });

    $(document).on("click", "#wcbe-bulk-edit-reset-filter", function () {
        wcbeResetFilters();
    });

    $(document).on("change", "#wcbe-quick-search-field", function () {
        let options = $("#wcbe-quick-search-operator option");
        switch ($(this).val()) {
            case "title":
            case "sku":
                options.each(function () {
                    $(this).closest("select").prop("selectedIndex", 0);
                    $(this).prop("disabled", false);
                });
                break;
            case "id":
                options.each(function () {
                    $(this).closest("select").prop("selectedIndex", 1);
                    if ($(this).attr("value") === "exact") {
                        $(this).prop("disabled", false);
                    } else {
                        $(this).prop("disabled", true);
                    }
                });
                break;
        }
    });

    // Quick Per Page
    $("#wcbe-quick-per-page").on("change", function () {
        wcbeChangeCountPerPage($(this).val());
    });

    $(document).on("click", ".wcbe-edit-action-with-button", function () {
        let productIds;
        let productData = [];
        let modal = $(this).closest(".wcbe-modal");

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        let wcbeValue;
        switch ($(this).attr("data-content-type")) {
            case "textarea":
                wcbeValue = tinymce.get("wcbe-text-editor").getContent();
                break;
            case "select_products":
                wcbeValue = $("#wcbe-select-products-value").val();
                break;
            case "multi_select":
                if (modal) {
                    wcbeValue = modal.find(".wcbe-modal-acf-taxonomy-multi-select-value").val();
                }
                break;
            case "select_files":
                let names = $(".wcbe-inline-edit-file-name")
                    .map(function () {
                        return $(this).val();
                    })
                    .get();

                let urls = $(".wcbe-inline-edit-file-url")
                    .map(function () {
                        return $(this).val();
                    })
                    .get();

                wcbeValue = {
                    files_name: names,
                    files_url: urls,
                };
                break;
            case "file":
                wcbeValue = $("#wcbe-modal-file #wcbe-file-id").val();
                break;
            case "image":
                wcbeValue = $(this).attr("data-image-id");
                break;
            case "gallery":
                wcbeValue = $("#wcbe-modal-gallery-items input.wcbe-inline-edit-gallery-image-ids")
                    .map(function () {
                        return $(this).val();
                    })
                    .get();
                break;
            case "custom_field_files":
                wcbeValue = [];
                if ($(".wcbe-modal-custom-field-file-item").length > 0) {
                    $(".wcbe-modal-custom-field-file-item").each(function () {
                        let name = $(this).find("input.wcbe-inline-edit-file-name").val();
                        let url = $(this).find("input.wcbe-inline-edit-file-url").val();
                        if (url != "") {
                            wcbeValue.push({
                                name: name,
                                url: url,
                            });
                        }
                    });
                }
                break;
        }

        productData.push({
            name: $(this).attr("data-name"),
            sub_name: $(this).attr("data-sub-name") ? $(this).attr("data-sub-name") : "",
            type: $(this).attr("data-update-type"),
            value: wcbeValue,
            operation: "inline_edit",
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", ".wcbe-load-text-editor", function () {
        tinymce.get("wcbe-text-editor").setContent("");

        let tdElement = $(this).closest("td");
        let productId = $(this).attr("data-item-id");
        let field = $(this).attr("data-field");
        let fieldType = $(this).attr("data-field-type");

        $("#wcbe-modal-text-editor-item-title").text($(this).attr("data-item-name"));
        $("#wcbe-text-editor-apply")
            .attr("data-field", field)
            .attr("data-field-type", fieldType)
            .attr("data-item-id", productId)
            .attr("data-update-type", tdElement.attr("data-update-type"))
            .attr("data-name", tdElement.attr("data-name"));

        $.ajax({
            url: WCBE_DATA.ajax_url,
            type: "post",
            dataType: "json",
            data: {
                action: "wcbe_get_text_editor_content",
                nonce: WCBE_DATA.ajax_nonce,
                product_id: productId,
                field: field,
                fetch_type: tdElement.attr("data-fetch-type"),
            },
            success: function (response) {
                if (response.success && response.content !== "") {
                    tinymce.get("wcbe-text-editor").setContent(response.content);
                    tinymce.execCommand("mceFocus", false, "wcbe-text-editor");
                }
            },
            error: function () { },
        });
    });

    $(document).on("click", "#wcbe-create-new-product-taxonomy", function () {
        if ($("#wcbe-new-product-category-name").val() !== "") {
            let taxonomyInfo = {
                name: $("#wcbe-new-product-taxonomy-name").val(),
                slug: $("#wcbe-new-product-taxonomy-slug").val(),
                parent: $("#wcbe-new-product-taxonomy-parent").val(),
                description: $("#wcbe-new-product-taxonomy-description").val(),
                product_id: $(this).attr("data-item-id"),
                modal_id: $(this).attr("data-closest-id"),
            };
            wcbeAddProductTaxonomy(taxonomyInfo, $(this).attr("data-field"), $(this).attr("data-item-id"));
        } else {
            swal({
                title: wcbeTranslate.taxonomyNameRequired,
                type: "warning",
            });
        }
    });

    //Search
    $(document).on("keyup", ".wcbe-search-in-list", function () {
        let wcbeSearchValue = this.value.toLowerCase().trim();
        $($(this).attr("data-id") + " .wcbe-product-items-list li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(wcbeSearchValue) > -1);
        });
    });

    $(document).on("click", "#wcbe-create-new-product-attribute", function () {
        if ($("#wcbe-new-product-attribute-name").val() !== "") {
            let attributeInfo = {
                name: $("#wcbe-new-product-attribute-name").val(),
                slug: $("#wcbe-new-product-attribute-slug").val(),
                description: $("#wcbe-new-product-attribute-description").val(),
                product_id: $(this).attr("data-item-id"),
            };
            wcbeAddProductAttribute(attributeInfo, $(this).attr("data-field"));
        } else {
            swal({
                title: wcbeTranslate.attributeNameRequired,
                type: "warning",
            });
        }
    });

    $(document).on("click", 'button[data-target="#wcbe-modal-select-products"]', function () {
        let childrenIds = $(this).attr("data-children-ids").split(",");
        let tdElement = $(this).closest("td");
        $("#wcbe-select-products-value").val("").change();
        $("#wcbe-modal-select-products-item-title").text($(this).attr("data-item-name"));
        $("#wcbe-modal-select-products .wcbe-edit-action-with-button")
            .attr("data-item-id", $(this).attr("data-item-id"))
            .attr("data-field", $(this).attr("data-field"))
            .attr("data-field-type", $(this).attr("data-field-type"))
            .attr("data-name", tdElement.attr("data-name"))
            .attr("data-update-type", tdElement.attr("data-update-type"));
        wcbeSetSelectedProducts(childrenIds);
    });

    $(document).on("click", "#wcbe-modal-select-files-add-file-item", function () {
        wcbeAddNewFileItem();
    });

    $(document).on("click", "#wcbe-modal-custom-field-files-add-file-item", function () {
        wcbeAddCustomFieldFileItem();
    });

    $(document).on("click", "#wcbe-bulk-edit-custom-field-files-add-file-item", function () {
        wcbeBulkEditAddCustomFieldFileItem();
    });

    $(document).on("click", 'button[data-toggle=modal][data-target="#wcbe-modal-select-files"]', function () {
        $(".wcbe-inline-select-files").html("");
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-select-files-apply")
            .attr("data-item-id", $(this).attr("data-item-id"))
            .attr("data-field", $(this).attr("data-field"))
            .attr("data-name", tdElement.attr("data-name"))
            .attr("data-update-type", tdElement.attr("data-update-type"));
        $("#wcbe-modal-select-files-item-title").text($(this).closest("td").attr("data-col-title"));
        wcbeGetProductFiles($(this).attr("data-item-id"));
    });

    $(document).on("click", 'button[data-toggle="modal"][data-target="#wcbe-modal-custom-field-files"]', function () {
        $(".wcbe-inline-custom-field-files").html("");
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-custom-field-files-apply")
            .attr("data-item-id", $(this).attr("data-item-id"))
            .attr("data-field", $(this).attr("data-field"))
            .attr("data-name", tdElement.attr("data-name"))
            .attr("data-update-type", tdElement.attr("data-update-type"));
        $("#wcbe-modal-custom-field-files-item-title").text($(this).closest("td").attr("data-col-title"));
        wcbeGetProductCustomFieldFiles($(this).attr("data-item-id"), tdElement.attr("data-name"));
    });

    $(document).on("click", ".wcbe-inline-edit-file-remove-item", function () {
        $(this).closest(".wcbe-modal-select-files-file-item").remove();
    });

    $(document).on("click", ".wcbe-custom-field-file-remove-item", function () {
        $(this).closest(".wcbe-modal-custom-field-file-item").remove();
    });

    $(document).on("click", ".wcbe-bulk-edit-custom-field-file-remove-item", function () {
        $(this).closest(".wcbe-bulk-edit-custom-field-file-item").remove();
    });

    if ($.fn.sortable) {
        let wcbeSelectFiles = $(".wcbe-inline-select-files");
        wcbeSelectFiles.sortable({
            handle: ".wcbe-select-files-sortable-btn",
            cancel: "",
        });
        wcbeSelectFiles.disableSelection();

        let wcbeCustomFieldFiles = $(".wcbe-inline-custom-field-files");
        wcbeCustomFieldFiles.sortable({
            handle: ".wcbe-custom-field-files-sortable-btn",
            cancel: "",
        });
        wcbeCustomFieldFiles.disableSelection();

        let wcbeBulkEditCustomFieldFiles = $(".wcbe-bulk-edit-custom-field-files");
        wcbeBulkEditCustomFieldFiles.sortable({
            handle: ".wcbe-bulk-edit-custom-field-files-sortable-btn",
            cancel: "",
        });
        wcbeBulkEditCustomFieldFiles.disableSelection();

        // yikes custom tabs
        let wcbeTabItems = $("#wcbe-modal-yikes-custom-tabs");
        wcbeTabItems.sortable({
            handle: ".wcbe-yikes-tab-item-sort",
            cancel: "",
        });
        wcbeTabItems.disableSelection();
    }

    $(document).on("change", ".wcbe-bulk-edit-form-variable", function () {
        let newVal = $(this).val() ? $(this).closest("div").find("input[type=text]").val() + "{" + $(this).val() + "}" : "";
        $(this).closest("div").find("input[type=text]").first().val(newVal).change();
    });

    $(document).on("change", 'select[data-field="operator"]', function () {
        let id = $(this).closest(".wcbe-form-group").find("label").attr("for");
        let $this = $(this);

        if ($(this).val() === "text_replace") {
            $(this)
                .closest(".wcbe-form-group")
                .append(
                    "<div class='wcbe-bulk-edit-form-extra-field'>" +
                    "<select id='" +
                    id +
                    "-sensitive' data-field='sensitive'>" +
                    "<option value='yes'>" +
                    wcbeTranslate.sameCase +
                    "</option>" +
                    "<option value='no'>" +
                    wcbeTranslate.ignoreCase +
                    "</option>" +
                    "</select>" +
                    "<input type='text' id='" +
                    id +
                    "-replace' data-field='replace' placeholder='" +
                    wcbeTranslate.enterText +
                    "'>" +
                    "<select class='wcbe-bulk-edit-form-variable' title='" +
                    wcbeTranslate.selectVariable +
                    "' data-field='variable'>" +
                    "<option value=''>" +
                    wcbeTranslate.variable +
                    "</option>" +
                    "<option value='title'>" +
                    wcbeTranslate.title +
                    "</option>" +
                    "<option value='id'>" +
                    wcbeTranslate.id +
                    "</option>" +
                    "<option value='sku'>" +
                    wcbeTranslate.sku +
                    "</option>" +
                    "<option value='menu_order'>Menu Order</option>" +
                    "<option value='parent_id'>" +
                    wcbeTranslate.parentId +
                    "</option>" +
                    "<option value='parent_title'>" +
                    wcbeTranslate.parentTitle +
                    "</option>" +
                    "<option value='parent_sku'>" +
                    wcbeTranslate.parentSku +
                    "</option>" +
                    "<option value='regular_price'>" +
                    wcbeTranslate.regularPrice +
                    "</option>" +
                    "<option value='sale_price'>" +
                    wcbeTranslate.salePrice +
                    "</option>" +
                    "</select>" +
                    "</div>"
                );
        } else if ($(this).val() === "number_round") {
            $(this)
                .closest(".wcbe-form-group")
                .append(
                    '<div class="wcbe-bulk-edit-form-extra-field"><select id="' +
                    id +
                    '-round-item"><option value="5">5</option><option value="10">10</option><option value="19">19</option><option value="29">29</option><option value="39">39</option><option value="49">49</option><option value="59">59</option><option value="69">69</option><option value="79">79</option><option value="89">89</option><option value="99">99</option></select></div>'
                );
        } else {
            $(this).closest(".wcbe-form-group").find(".wcbe-bulk-edit-form-extra-field").remove();
        }
        if ($.inArray($(this).val(), ["number_clear", "text_remove_duplicate", "text_clear"]) !== -1) {
            $(this).closest(".wcbe-form-group").find('[data-field="value"]').val("").prop("disabled", true);
            $(this).closest(".wcbe-form-group").find('select[data-field="variable"]').val("").prop("disabled", true);
        } else {
            $(this).closest(".wcbe-form-group").find('[data-field="value"]').prop("disabled", false);
            $(this).closest(".wcbe-form-group").find('select[data-field="variable"]').prop("disabled", false);
        }

        if (!$(this).closest("#wcbe-variations-bulk-actions-modal").length) {
            setTimeout(function () {
                changedTabs($this);
            }, 150);
        }
    });

    $("#wcbe-float-side-modal-bulk-edit, #wcbe-float-side-modal-bulk-new-products, #wcbe-float-side-modal-filter").on(
        "change",
        '[data-field="value"], [data-field="from"], [data-field="to"]',
        function () {
            changedTabs($(this));
        }
    );

    $(document).on("change", ".wcbe-date-from", function () {
        let field_to = $("#" + $(this).attr("data-to-id"));
        let datepicker = true;
        let timepicker = false;
        let format = "Y/m/d";

        if ($(this).hasClass("wcbe-datetimepicker")) {
            timepicker = true;
            format = "Y/m/d H:i";
        }

        if ($(this).hasClass("wcbe-timepicker")) {
            datepicker = false;
            timepicker = true;
            format = "H:i";
        }

        field_to.val("");
        field_to.datetimepicker("destroy");
        field_to.datetimepicker({
            format: format,
            datepicker: datepicker,
            timepicker: timepicker,
            minDate: $(this).val(),
        });
    });

    $(document).on("click", ".wcbe-bulk-edit-form-remove-image", function () {
        $(this).closest("div").remove();
        $(".wcbe-bulk-edit-form-product-image").val("");
    });

    $(document).on("click", ".wcbe-bulk-edit-form-remove-gallery-item", function () {
        $(this).closest("div").remove();
        $("#wcbe-bulk-edit-form-product-gallery input[value=" + $(this).attr("data-id") + "]").remove();
    });

    $(document).on("click", ".wcbe-sortable-column", function () {
        let otherIcons = $(".wcbe-sortable-column")
            .not('[data-column-name="' + $(this).attr("data-column-name") + '"]')
            .find(".wcbe-sortable-column-icon i");
        if (otherIcons.length) {
            otherIcons.each(function () {
                $(this).closest(".wcbe-sortable-column-icon").html(WCBE_DATA.icons.sortUpAndDown);
            });
        }

        let currentSortType = $(this).attr("data-sort") === undefined ? WCBE_DATA.wcbe_settings.default_sort : $(this).attr("data-sort");
        if (currentSortType === "DESC") {
            $(this).attr("data-sort", "ASC");
            wcbeSortByColumn($(this).attr("data-column-name"), "ASC");
            $(this).find(".wcbe-sortable-column-icon").html(WCBE_DATA.icons.sortDownToUp);
        } else {
            $(this).attr("data-sort", "DESC");
            $(this).find(".wcbe-sortable-column-icon").html(WCBE_DATA.icons.sortUpToDown);
            wcbeSortByColumn($(this).attr("data-column-name"), "DESC");
        }
    });

    $(document).on("click", ".wcbe-column-manager-edit-field-btn", function () {
        $("#wcbe-modal-column-manager-edit-preset .wcbe-box-loading").show();
        let presetKey = $(this).val();
        $("#wcbe-modal-column-manager-edit-preset .items").html("");
        $("#wcbe-column-manager-edit-preset-key").val(presetKey);
        $("#wcbe-column-manager-edit-preset-name").val($(this).attr("data-preset-name"));
        wcbeColumnManagerFieldsGetForEdit(presetKey);
    });

    $(document).on("click", "#wcbe-get-meta-fields-by-product-id", function () {
        $(".wcbe-meta-fields-empty-text").hide();
        let input = $("#wcbe-add-meta-fields-product-id");
        wcbeAddMetaKeysByProductID(input.val());
        input.val("");
    });

    $(document).on("click", "#wcbe-bulk-edit-undo", function () {
        $(this).prop("disabled", true);
        wcbeHistoryUndo();
    });

    $(document).on("click", "#wcbe-bulk-edit-redo", function () {
        $(this).prop("disabled", true);
        wcbeHistoryRedo();
    });

    $(document).on("click", "#wcbe-history-filter-apply", function () {
        let filters = {
            operation: $("#wcbe-history-filter-operation").val(),
            author: $("#wcbe-history-filter-author").val(),
            fields: $("#wcbe-history-filter-fields").val(),
            date: {
                from: $("#wcbe-history-filter-date-from").val(),
                to: $("#wcbe-history-filter-date-to").val(),
            },
        };
        wcbeHistoryFilter(filters);
    });

    $(document).on("click", "#wcbe-history-filter-reset", function () {
        $(".wcbe-history-filter-fields input").val("");
        $(".wcbe-history-filter-fields select").val("").change();
        wcbeHistoryFilter();
    });

    $(document).on("click", '[data-target="#wcbe-float-side-modal-history"]', function () {
        if ($(this).attr("data-loaded") == "true") {
            return;
        } else {
            $(this).attr("data-loaded", "true");
            $(".wcbe-history-filter-fields input").val("");
            $(".wcbe-history-filter-fields select").val("").change();
            wcbeHistoryFilter(null, false);
        }
    });

    $(document).on("click", '[data-target="#wcbe-float-side-modal-meta-fields"]', function () {
        if ($(this).attr("data-loaded") == "true") {
            return;
        } else {
            $(this).attr("data-loaded", "true");
            wcbeMetaFieldsLoad();
        }
    });

    $(document).on("change", ".wcbe-meta-fields-main-type", function () {
        let item = $(this).closest(".wcbe-meta-fields-right-item");
        if ($(this).val() === "textinput") {
            item.find(".wcbe-meta-fields-sub-type").show();
        } else {
            item.find(".wcbe-meta-fields-sub-type").hide();
        }

        if ($.inArray($(this).val(), ["select", "array", "radio"]) !== -1) {
            item.find(".wcbe-meta-fields-key-value").show();
        } else {
            item.find(".wcbe-meta-fields-key-value").hide();
        }
    });

    $("#wcbe-column-manager-add-new-preset").on("submit", function (e) {
        if ($(this).find(".wcbe-column-manager-added-fields .items .wcbe-column-manager-right-item").length < 1) {
            e.preventDefault();
            swal({
                title: wcbeTranslate.plzAddColumns,
                type: "warning",
            });
        }
    });

    $(document).on("click", "#wcbe-bulk-edit-form-reset, #wcbe-bulk-new-form-reset", function () {
        $(this).prop("disabled", true);
        wcbeResetBulkEditForm();
    });

    $(document).on("click", "#wcbe-filter-form-save-preset", function () {
        $(this).prop("disabled", true);
        let presetName = $("#wcbe-filter-form-save-preset-name").val();
        if (presetName !== "") {
            if ($('[data-target="#wcbe-float-side-modal-filter-profiles"]').attr("data-loaded") != "true") {
                wcbeFilterProfileLoad();
            }
            let data = wcbeGetProSearchData();
            wcbeSaveFilterPreset(data, presetName);
        } else {
            swal({
                title: wcbeTranslate.presetNameRequired,
                type: "warning",
            });
            $(this).prop("disabled", false);
        }
    });

    $(document).on("click", ".wcbe-bulk-edit-form-do-bulk-edit", function (e) {
        $(this).prop("disabled", true);
        wcbeProductEdit(wcbeGetProductsChecked(), wcbeGetBulkEditData());
    });

    $(document).on("click", '[data-target="#wcbe-modal-new-item"]', function () {
        $("#wcbe-new-item-title").html(wcbeTranslate.newProduct);
        $("#wcbe-new-item-description").html(wcbeTranslate.newProductNumber);
    });

    $(document).on("click", '[data-target="#wcbe-modal-text-editor"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-text-editor-item-title").html($(this).attr(""));
        $("#wcbe-text-editor-apply").attr("data-name", tdElement.attr("data-name")).attr("data-update-type", tdElement.attr("data-update-type"));
    });

    // keypress: Enter
    $(document).on("keypress", function (e) {
        if (e.keyCode === 13) {
            if ($("#wcbe-quick-search-text").val() !== "" && $($("#wcbe-last-modal-opened").val()).css("display") !== "block" && $(".wcbe-tabs-list a[data-content=bulk-edit]").hasClass("selected")) {
                wcbeReloadProducts();
                $("#wcbe-quick-search-reset").show();
                $(".wcbe-quick-filter a").addClass("active");
            }
            if ($("#wcbe-modal-new-product-taxonomy").css("display") === "block") {
                $("#wcbe-create-new-product-taxonomy").trigger("click");
            }
            if ($("#wcbe-modal-new-item").css("display") === "block") {
                if ($("#wcbe-create-new-item").prop("disabled") === false) {
                    $("#wcbe-create-new-item").trigger("click");
                }
            }
            if ($("#wcbe-modal-item-duplicate").css("display") === "block") {
                if ($("#wcbe-bulk-edit-duplicate-start").prop("disabled") === false) {
                    $("#wcbe-bulk-edit-duplicate-start").trigger("click");
                }
            }

            let metaFieldManualInput = $("#wcbe-meta-fields-manual_key_name");
            let metaFieldProductId = $("#wcbe-add-meta-fields-product-id");
            if (metaFieldManualInput.val() !== "" && $("#wcbe-add-meta-field-manual").prop("disabled") === false) {
                $(".wcbe-meta-fields-empty-text").hide();
                wcbeAddMetaKeysManual(metaFieldManualInput.val().toLowerCase());
                metaFieldManualInput.val("");
            }
            if (metaFieldProductId.val() !== "") {
                $(".wcbe-meta-fields-empty-text").hide();
                wcbeAddMetaKeysByProductID(metaFieldProductId.val());
                metaFieldProductId.val("");
            }

            // filter form
            if ($("#wcbe-float-side-modal-filter:visible").length) {
                $("#wcbe-float-side-modal-filter:visible").find(".wcbe-filter-form-action").trigger("click");
            }
        }
    });

    $(document).on("click", ".wcbe-bulk-edit-status-filter-item", function () {
        $(".wcbe-top-nav-status-filter").hide();

        $(".wcbe-bulk-edit-status-filter-item").removeClass("active");
        $(this).addClass("active");
        $(".wcbe-status-filter-selected-name").text(" - " + $(this).text());

        if ($(this).attr("data-status") === "all") {
            $("#wcbe-filter-form-reset").trigger("click");
        } else {
            $("#wcbe-filter-form-product-status").val($(this).attr("data-status")).change();
            setTimeout(function () {
                $("#wcbe-filter-form-get-products").trigger("click");
            }, 250);
        }
    });

    $(document).on("click", ".wcbe-reset-filter-form", function () {
        wcbeResetFilters();
    });

    $(document).on("click", "#wcbe-filter-form-get-products", function () {
        $(this).prop("disabled", true);
        wcbeFilterFormCheckAttributes();
        wcbeCheckResetFilterButton();
    });

    $(document).on("select2:select", "#wcbe-variation-bulk-edit-attributes", function (e) {
        getAttributeValues(e.params.data.id, "#wcbe-variation-bulk-edit-attributes-added");
    });

    $(document).on("select2:select", "#wcbe-variation-bulk-edit-delete-attributes", function (e) {
        getAttributeValuesForDelete(e.params.data.id, "#wcbe-variation-bulk-edit-delete-attributes-added");
    });

    $(document).on("select2:unselect", "#wcbe-variation-bulk-edit-attributes", function (e) {
        $("div[data-id=wcbe-variation-bulk-edit-attribute-item-" + e.params.data.id + "]").remove();
        $(".wcbe-variation-bulk-edit-attribute-item[data-id=" + e.params.data.id + "]").remove();
    });

    $(document).on("select2:unselect", "#wcbe-variation-bulk-edit-delete-attributes", function (e) {
        $("div[data-id=wcbe-variation-bulk-edit-delete-attribute-item-" + e.params.data.id + "]").remove();
    });

    $(document).on("change", "#wcbe-variation-single-delete-items input:checkbox", function () {
        if ($("#wcbe-variation-single-delete-items input:checkbox:checked").length > 0) {
            $("#wcbe-variation-delete-selected").prop("disabled", false);
        } else {
            $("#wcbe-variation-delete-selected").attr("disabled", "disabled");
        }
    });

    $(document).on("change", "#wcbe-variation-bulk-edit-attributes", function () {
        if ($(this).val() === null) {
            $("#wcbe-variation-bulk-edit-generate").attr("disabled", "disabled");
            $("#wcbe-variation-bulk-edit-manual-add").attr("disabled", "disabled");
        }
    });
    $(document).on("change", "#wcbe-variation-bulk-edit-attributes", function () {
        if ($(this).val() === null) {
            $("#wcbe-variation-bulk-edit-generate").attr("disabled", "disabled");
            $("#wcbe-variation-bulk-edit-manual-add").attr("disabled", "disabled");
        }
    });

    $(document).on("click", ".wcbe-inline-edit-taxonomy-save", function () {
        let productData = [];
        let productIds;

        let value = $("#wcbe-modal-product-taxonomy input:checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        productData.push({
            name: $(this).attr("data-name"),
            sub_name: $(this).attr("data-sub-name") ? $(this).attr("data-sub-name") : "",
            type: $(this).attr("data-update-type"),
            value: value,
            operation: "inline_edit",
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", ".wcbe-product-attribute", function () {
        let modalId = $(this).attr("data-target");
        $(modalId)
            .find("input.is-visible")
            .prop("checked", $(this).attr("data-is-variation") == "true")
            .change();
        $(modalId)
            .find("input.is-visible-prev")
            .val($(this).attr("data-is-visible") == "true" ? "yes" : "no");
        $(modalId)
            .find("input.is-variation")
            .prop("checked", $(this).attr("data-is-variation") == "true")
            .change();
        $(modalId)
            .find("input.is-variation-prev")
            .val($(this).attr("data-is-variation") == "true" ? "yes" : "no");
    });

    $(document).on("click", ".wcbe-inline-edit-attribute-save", function () {
        let productData = [];
        let productIds;
        let modal = $("#wcbe-modal-product-attribute");
        let value = modal
            .find(".wcbe-modal-product-attribute-terms-list input:checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        productData.push({
            name: $(this).attr("data-name"),
            sub_name: $(this).attr("data-sub-name") ? $(this).attr("data-sub-name") : "",
            type: $(this).attr("data-update-type"),
            value: value,
            used_for_variations: modal.find("input.is-variation").prop("checked") === true ? "yes" : "no",
            used_for_variations_prev: modal.find("input.is-variation-prev").val(),
            attribute_is_visible: modal.find("input.is-visible").prop("checked") === true ? "yes" : "no",
            attribute_is_visible_prev: modal.find("input.is-visible-prev").val(),
            operation: "inline_edit",
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", ".wcbe-inline-edit-add-new-taxonomy", function () {
        $("#wcbe-create-new-product-taxonomy").attr("data-field", $(this).attr("data-field")).attr("data-item-id", $(this).attr("data-item-id")).attr("data-closest-id", $(this).attr("data-closest-id"));
        $("#wcbe-modal-new-product-taxonomy-product-title").text($(this).attr("data-item-name"));
        wcbeGetTaxonomyParentSelectBox($(this).attr("data-field"));
    });

    $(document).on("click", ".wcbe-inline-edit-add-new-attribute", function () {
        $("#wcbe-create-new-product-attribute").attr("data-field", $(this).attr("data-field")).attr("data-item-id", $(this).attr("data-item-id"));
        $("#wcbe-modal-new-product-attribute-item-title").text($(this).attr("data-item-name"));
    });

    $(document).on("click", 'button.wcbe-calculator[data-target="#wcbe-modal-numeric-calculator"]', function () {
        let btn = $("#wcbe-modal-numeric-calculator .wcbe-edit-action-numeric-calculator");
        let tdElement = $(this).closest("td");
        btn.attr("data-item-id", $(this).attr("data-item-id"));
        btn.attr("data-field", $(this).attr("data-field"));
        btn.attr("data-name", tdElement.attr("data-name"));
        btn.attr("data-update-type", tdElement.attr("data-update-type"));
        btn.attr("data-field-type", $(this).attr("data-field-type"));
        if ($(this).attr("data-field") === "download_limit" || $(this).attr("data-field") === "download_expiry") {
            $("#wcbe-modal-numeric-calculator #wcbe-numeric-calculator-type").val("n").change().hide();
            $("#wcbe-modal-numeric-calculator #wcbe-numeric-calculator-round").val("").change().hide();
        } else {
            $("#wcbe-modal-numeric-calculator #wcbe-numeric-calculator-type").show();
            $("#wcbe-modal-numeric-calculator #wcbe-numeric-calculator-round").show();
        }
        $("#wcbe-modal-numeric-calculator-item-title").text($(this).attr("data-item-name"));
    });

    $(document).on("click", ".wcbe-edit-action-numeric-calculator", function () {
        let productId = $(this).attr("data-item-id");
        let productIds;
        let productData = [];

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push(productId);
        }

        productData.push({
            name: $(this).attr("data-name"),
            sub_name: $(this).attr("data-name") ? $(this).attr("data-name") : "",
            type: $(this).attr("data-update-type"),
            operator: $("#wcbe-numeric-calculator-operator").val(),
            value: $("#wcbe-numeric-calculator-value").val(),
            operator_type: $("#wcbe-numeric-calculator-type").val() ? $("#wcbe-numeric-calculator-type").val() : "n",
            round: $("#wcbe-numeric-calculator-round").val(),
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("keyup", "input[type=number][data-field=download_limit], input[type=number][data-field=download_expiry]", function () {
        if ($(this).val() < -1) {
            $(this).val(-1);
        }
    });

    $(document).on("click", "#wcbe-quick-search-button", function () {
        if ($("#wcbe-quick-search-text").val() !== "") {
            $("#wcbe-quick-search-reset").show();
            $(".wcbe-quick-filter a").addClass("active");
        }
    });

    $(document).on("click", "#wcbe-quick-search-reset", function () {
        wcbeResetFilters();
    });

    $(document).on("click", 'button[data-toggle="modal"][data-target="#wcbe-modal-product-badges"]', function () {
        $("#wcbe-modal-product-badges-item-title").text($(this).attr("data-item-name"));
        $("#wcbe-modal-product-badges-apply").attr("data-item-id", $(this).attr("data-item-id"));
        $("#wcbe-modal-product-badge-items").val("").change();
        wcbeGetProductBadges($(this).attr("data-item-id"));
    });

    $(document).on("click", 'button[data-toggle="modal"][data-target="#wcbe-modal-ithemeland-badge"]', function () {
        let productId = $(this).attr("data-item-id");
        $("#wcbe-modal-ithemeland-badge-item-title").text($(this).attr("data-item-name"));
        $("#wcbe-modal-ithemeland-badge-apply").attr("data-item-id", productId);
        $(".it_unique_nav_for_general").trigger("click");
        $("#_unique_label_type").val("none").change();
        wcbeGetProductIthemelandBadge(productId);
    });

    $(document).on("click", 'button[data-toggle="modal"][data-target="#wcbe-modal-yikes-custom-product-tabs"]', function () {
        $("#wcbe-modal-yikes-custom-tabs").html("");
        let productId = $(this).attr("data-item-id");
        $("#wcbe-modal-yikes-custom-product-tabs-item-title").text($(this).attr("data-item-name"));
        $("#wcbe-modal-yikes-custom-product-tabs-apply").attr("data-item-id", productId);
        wcbeGetYikesCustomProductTabs(productId);
    });

    $(document).on("click", "#wcbe-modal-product-badges-apply", function () {
        let productIds = [];
        let productData = [];
        productIds.push($(this).attr("data-item-id"));
        productData.push({
            name: "_yith_wcbm_product_meta",
            sub_name: "id_badge",
            type: "meta_field",
            operation: "inline_edit",
            value: $("#wcbe-modal-product-badge-items").val(),
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", "#wcbe-yikes-add-tab", function () {
        let newUniqueId = "editor-" + Math.floor(Math.random() * 9999 + 1000);
        $("#wcbe-modal-yikes-custom-product-tabs #duplicate-item")
            .clone()
            .appendTo("#wcbe-modal-yikes-custom-tabs")
            .ready(function () {
                let duplicated = $("#wcbe-modal-yikes-custom-tabs").find("#duplicate-item");
                duplicated.find(".wcbe-yikes-tab-content").attr("data-id", newUniqueId).find("textarea").attr("id", newUniqueId);
                duplicated.removeAttr("id");
                wp.editor.initialize(newUniqueId, wcbeWpEditorSettings);
            });
    });

    $(document).on("click", "#wcbe-modal-ithemeland-badge-apply", function () {
        let productIds;
        let productData = [];
        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        $('#ithemeland-badge-form .ithemeland-badge-form-item[data-value-position="child"]').each(function () {
            let fieldName = $(this).attr("data-name");
            let value;
            switch ($(this).attr("data-type")) {
                case "text":
                case "dropdown":
                    value = $('input[name="' + fieldName + '"]').val();
                    break;
                case "radio":
                case "checkbox":
                    value = $('input[name="' + fieldName + '"]:checked').val();
                    break;
            }
            productData.push({
                name: fieldName,
                type: "meta_field",
                value: value,
                operation: "inline_edit",
            });
        });

        $('#ithemeland-badge-form .ithemeland-badge-form-item[data-value-position="self"]').each(function () {
            let value;
            if ($(this).attr("type") === "checkbox") {
                value = $(this).prop("checked") === true ? "yes" : "no";
            } else {
                value = $(this).val();
            }
            productData.push({
                name: $(this).attr("name"),
                type: "meta_field",
                value: value,
                operation: "inline_edit",
            });
        });

        wcbeProductEdit(productIds, productData);
    });

    $("#wcbe-bulk-edit-select-all-variations").prop("checked", false);

    if (itemIdInUrl && itemIdInUrl > 0) {
        wcbeResetFilterForm();
        setTimeout(function () {
            $("#wcbe-filter-form-product-ids").val(itemIdInUrl);
            $("#wcbe-filter-form-get-products").trigger("click");
        }, 500);
    }

    $(document).on("click", ".wcbe-yikes-tab-item-header", function (e) {
        if ($.inArray($(e.target).attr("class"), ["wcbe-yikes-tab-item-header", "wcbe-yikes-tab-item-header-title"]) !== -1) {
            if ($(this).closest("div.wcbe-yikes-tab-item").find(".wcbe-yikes-tab-item-body:visible").length > 0) {
                $(".wcbe-yikes-tab-item-body").slideUp(250);
            } else {
                $(".wcbe-yikes-tab-item-body").slideUp(250);
                $(this).closest("div.wcbe-yikes-tab-item").find(".wcbe-yikes-tab-item-body").slideDown(250);
            }
        }
    });

    $(document).on("keyup", ".wcbe-yikes-tab-title input", function () {
        $(this).closest(".wcbe-yikes-tab-item").find(".wcbe-yikes-tab-item-header strong").text($(this).val());
    });

    $(document).on("click", ".wcbe-yikes-tab-item-remove", function () {
        $(this).closest(".wcbe-yikes-tab-item").remove();
    });

    $(document).on("click", "#wcbe-modal-yikes-custom-product-tabs-apply", function () {
        let productIds;
        let productData = [];
        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        let tabs = [];
        let customProductTabsElement = $(this).closest("#wcbe-modal-yikes-custom-product-tabs").find("#yikes-custom-product-tabs-form .wcbe-yikes-tab-item");
        if (customProductTabsElement.length > 0) {
            customProductTabsElement.each(function () {
                let editorId = $(this).find(".wcbe-yikes-tab-content").attr("data-id");
                tabs.push({
                    global_tab: $(this).find('input[name="global_tab"]').val(),
                    title: $(this).find(".wcbe-yikes-tab-title input").val(),
                    content: tinymce.get(editorId).getContent(),
                });
            });
        }

        productData.push({
            name: "yikes_woo_products_tabs",
            type: "meta_field",
            value: tabs,
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", "#wcbe-yikes-add-saved-tab", function () {
        $("#wcbe-last-modal-opened").val(".wcbe-yikes-saved-tabs");
        $(this).closest("#wcbe-modal-yikes-custom-product-tabs").find(".wcbe-yikes-saved-tabs").fadeIn(250);
    });

    $(document).on("click", ".wcbe-yikes-saved-tabs-close-button", function () {
        $(this).closest(".wcbe-yikes-saved-tabs").fadeOut(250);
        $("#wcbe-last-modal-opened").val("#wcbe-modal-yikes-custom-product-tabs");
    });

    $(document).on("click", ".wcbe-yikes-saved-tab-add", function () {
        wcbeAddYikesSavedTab($(this).attr("data-id"));
        $(".wcbe-yikes-saved-tabs-close-button").trigger("click");
    });

    $(document).on("change", ".wcbe-yikes-override-tab", function () {
        let tabItem = $(this).closest(".wcbe-yikes-tab-item");
        let globalInput = tabItem.find('input[name="global_tab"]');
        if ($(this).prop("checked") === false) {
            globalInput.val(globalInput.attr("data-global-id"));
            tabItem.find(".wcbe-yikes-tab-title input").prop("disabled", "disabled");
            tabItem.find(".wcbe-yikes-tab-content button").prop("disabled", "disabled");
            tinyMCE.get(tabItem.find(".wcbe-yikes-tab-content").attr("data-id")).getBody().setAttribute("contenteditable", false);
        } else {
            globalInput.val("");
            tabItem.find(".wcbe-yikes-tab-title input").prop("disabled", false);
            tabItem.find(".wcbe-yikes-tab-content button").prop("disabled", false);
            tinyMCE.get(tabItem.find(".wcbe-yikes-tab-content").attr("data-id")).getBody().setAttribute("contenteditable", true);
        }
    });

    $(document).on("click", '[data-toggle="modal"][data-target="#wcbe-modal-gallery"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-gallery #wcbe-modal-gallery-items").html("");
        $("#wcbe-modal-gallery #wcbe-modal-gallery-title").text($(this).attr("data-item-name"));
        $("#wcbe-modal-gallery #wcbe-modal-gallery-apply")
            .attr("data-item-id", $(this).attr("data-item-id"))
            .attr("data-name", tdElement.attr("data-name"))
            .attr("data-update-type", tdElement.attr("data-update-type"));
        wcbeGetProductGalleryImages($(this).attr("data-item-id"));
    });

    $(document).on("click", ".wcbe-delete-item-btn", function () {
        let productIds = [];
        productIds.push($(this).attr("data-item-id"));
        let deleteType = $(this).attr("data-delete-type");
        swal(
            {
                title: wcbeTranslate.areYouSure,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeDeleteProduct(productIds, deleteType);
                }
            }
        );
    });

    $(document).on("click", ".wcbe-restore-item-btn", function () {
        let productIds = [];
        productIds.push($(this).attr("data-item-id"));
        swal(
            {
                title: wcbeTranslate.areYouSure,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeRestoreProduct(productIds);
                }
            }
        );
    });

    $(document).on("change", "#wcbe-filter-form-product-status", function () {
        if ($(this).val() == "trash") {
            $(".wcbe-nav-trash-button").find('div[data-page="general"]').hide();
            $(".wcbe-nav-trash-button").find('div[data-page="trash"]').show();
        } else {
            $(".wcbe-nav-trash-button").find('div[data-page="general"]').show();
            $(".wcbe-nav-trash-button").find('div[data-page="trash"]').hide();
        }
    });

    $(document).on("click", ".wcbe-trash-option-restore-selected-items", function () {
        let productIds = wcbeGetProductsChecked();
        if (!productIds.length) {
            swal({
                title: "Please select one product",
                type: "warning",
            });
            return false;
        } else {
            swal(
                {
                    title: wcbeTranslate.areYouSure,
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                    confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                    confirmButtonText: wcbeTranslate.iAmSure,
                    closeOnConfirm: true,
                },
                function (isConfirm) {
                    if (isConfirm) {
                        if (wcbeSelectAllChecked()) {
                            productIds = "all_filtered";
                        }
                        wcbeLoadingStart();
                        wcbeRestoreProduct(productIds);
                    }
                }
            );
        }
    });

    $(document).on("click", ".wcbe-trash-option-restore-all", function () {
        swal(
            {
                title: wcbeTranslate.areYouSure,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeRestoreProduct([]);
                    wcbeLoadingStart();
                }
            }
        );
    });

    $(document).on("click", ".wcbe-trash-option-delete-selected-items", function () {
        let productIds = wcbeGetProductsChecked();
        if (!productIds.length) {
            swal({
                title: "Please select one product",
                type: "warning",
            });
            return false;
        } else {
            swal(
                {
                    title: wcbeTranslate.areYouSure,
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                    confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                    confirmButtonText: wcbeTranslate.iAmSure,
                    closeOnConfirm: true,
                },
                function (isConfirm) {
                    if (isConfirm) {
                        if (wcbeSelectAllChecked()) {
                            productIds = "all_filtered";
                        }

                        wcbeLoadingStart();
                        wcbeDeleteProduct(productIds, "permanently");
                    }
                }
            );
        }
    });

    $(document).on("click", ".wcbe-trash-option-delete-all", function () {
        swal(
            {
                title: wcbeTranslate.areYouSure,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeEmptyTrash();
                    wcbeLoadingStart();
                }
            }
        );
    });

    $(document).on("click", "#wcbe-bulk-edit-trash-empty", function () {
        swal(
            {
                title: wcbeTranslate.areYouSure,
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeEmptyTrash();
                }
            }
        );
    });

    $(document).on("click", "#wcbe-bulk-edit-trash-restore", function () {
        let productIds = wcbeGetProductsChecked();
        wcbeRestoreProduct(productIds);
    });

    $(document).on("click", '[data-toggle="modal"][data-target="#wcbe-modal-it-wc-dynamic-pricing-all-fields"]', function () {
        let tdElement = $(this).closest("td");
        let productType = $(this).attr("data-item-type");

        $("#wcbe-modal-it-wc-dynamic-pricing-all-fields .wcbe-modal-section").each(function () {
            let sectionType = $(this).attr("data-type").split(",");
            if ($.inArray(productType, sectionType) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        $("#wcbe-modal-it-wc-dynamic-pricing-all-fields").find('input[type="number"]').val("").change();
        $("#wcbe-modal-it-wc-dynamic-pricing-all-fields").find("select").val("").change();
        $("#wcbe-modal-it-wc-dynamic-pricing-all-fields").find('input[type="checkbox"]').prop("checked", false);

        $("#wcbe-modal-it-wc-dynamic-pricing-all-fields-title").text($(this).attr("data-item-name"));
        $("#wcbe-modal-it-wc-dynamic-pricing-all-fields-apply")
            .attr("data-item-id", $(this).attr("data-item-id"))
            .attr("data-name", tdElement.attr("data-name"))
            .attr("data-update-type", tdElement.attr("data-update-type"));
        wcbeGetItWcDynamicPricingAllFields($(this).attr("data-item-id"));
    });

    $(document).on("click", '[data-toggle="modal"][data-target="#wcbe-modal-it-wc-dynamic-pricing"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-it-wc-dynamic-pricing").find('input[data-type="value"]').val("").change();
        $("#wcbe-modal-it-wc-dynamic-pricing-title").text($(this).attr("data-item-name"));
        $("#wcbe-modal-it-wc-dynamic-pricing-apply")
            .attr("data-item-id", $(this).attr("data-item-id"))
            .attr("data-name", tdElement.attr("data-name"))
            .attr("data-update-type", tdElement.attr("data-update-type"));
        wcbeGetItWcRolePrices($(this).attr("data-item-id"));
    });

    $(document).on("click", '[data-toggle="modal"][data-target="#wcbe-modal-it-wc-dynamic-pricing-select-roles"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-it-wc-dynamic-pricing-select-roles #wcbe-user-roles").val("").change();
        $("#wcbe-modal-it-wc-dynamic-pricing-select-roles-title").text($(this).attr("data-item-name"));
        $("#wcbe-modal-it-wc-dynamic-pricing-select-roles-apply")
            .attr("data-item-id", $(this).attr("data-item-id"))
            .attr("data-name", tdElement.attr("data-name"))
            .attr("data-update-type", tdElement.attr("data-update-type"));
        wcbeGetItWcDynamicPricingSelectedRoles($(this).attr("data-item-id"), tdElement.attr("data-name"));
    });

    $(document).on("click", "#wcbe-modal-it-wc-dynamic-pricing-apply", function () {
        let productIds;
        let productData = [];
        let values = [];

        $(this)
            .closest("#wcbe-modal-it-wc-dynamic-pricing")
            .find('input[data-type="value"]')
            .each(function () {
                if ($(this).val()) {
                    values.push({
                        field: $(this).attr("data-name"),
                        amount: $(this).val(),
                    });
                }
            });

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        productData.push({
            name: $(this).attr("data-name"),
            type: $(this).attr("data-update-type"),
            operation: "inline_edit",
            value: values,
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", "#wcbe-modal-it-wc-dynamic-pricing-select-roles-apply", function () {
        let productIds;
        let productData = [];

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        productData.push({
            name: $(this).attr("data-name"),
            type: $(this).attr("data-update-type"),
            operation: "inline_edit",
            value: $(this).closest("#wcbe-modal-it-wc-dynamic-pricing-select-roles").find("#wcbe-user-roles").val(),
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", ".wcbe-acf-taxonomy-multi-select", function () {
        $(".wcbe-modal-acf-taxonomy-multi-select-value").select2();
    });

    $(document).on("click", "#wcbe-modal-it-wc-dynamic-pricing-all-fields-apply", function () {
        let productIds;
        let productData = [];

        let pricing_roles = [];

        $(this)
            .closest("#wcbe-modal-it-wc-dynamic-pricing-all-fields")
            .find('#wcbe-it-pricing-roles input[data-type="value"]')
            .each(function () {
                if ($(this).val()) {
                    pricing_roles.push({
                        field: $(this).attr("data-name"),
                        amount: $(this).val(),
                    });
                }
            });

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push($(this).attr("data-item-id"));
        }

        productData.push({
            name: $(this).attr("data-name"),
            type: $(this).attr("data-update-type"),
            operation: "inline_edit",
            value: {
                it_product_disable_discount: $("#wcbe-it-wc-dynamic-pricing-disable-discount").prop("checked") === true ? "yes" : "no",
                it_product_hide_price_unregistered: $("#wcbe-it-wc-dynamic-pricing-hide-price-unregistered").prop("checked") === true ? "yes" : "no",
                pricing_rules_product: pricing_roles,
                it_pricing_product_price_user_role: $("#wcbe-select-roles-hide-price").val(),
                it_pricing_product_add_to_cart_user_role: $("#wcbe-select-roles-hide-add-to-cart").val(),
                it_pricing_product_hide_user_role: $("#wcbe-select-roles-hide-product").val(),
            },
        });

        wcbeProductEdit(productIds, productData);
    });

    // Compatible with ithemeland badge

    // Only show the "remove image" button when needed
    if (!$("#_unique_label_image").val()) {
        $(".it_remove_image_button").hide();
        $("#_unique_label_image").val("http://wordpress.local/wp-content/plugins/woocommerce-advanced-product-labels/assets/admin/images/placeholder.png");
        $(".product-label img").css({
            width: "50px",
        });
    }

    // Uploading files
    var file_frame;
    $(document).on("click", ".it_upload_image_button", function (event) {
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.downloadable_file = wp.media({
            title: "Choose an image",
            button: {
                text: "Use image",
            },
            multiple: false,
        });

        // When an image is selected, run a callback.
        file_frame.on("select", function () {
            attachment = file_frame.state().get("selection").first().toJSON();

            $("#_unique_label_image").val(attachment.url);
            $("#unique_thumbnail img").attr("src", attachment.url);
            $(".it_remove_image_button").show();
            if ($(".custom_label_pic").length < 1) {
                $(".product-label").wrapInner("<img class='custom_label_pic' style='width: auto;' src='" + attachment.url + "'/>");
            } else {
                $(".product-label img").attr("src", attachment.url);
            }
            $(".product-label img").css({
                width: "auto",
            });
        });

        // Finally, open the modal.
        file_frame.open();
    });

    $(document).on("click", ".it_remove_image_button", function (event) {
        $("#unique_thumbnail img").attr("src", "http://wordpress.local/wp-content/plugins/woocommerce-advanced-product-labels/assets/admin/images/placeholder.png");
        $(".product-label img").attr("src", "http://wordpress.local/wp-content/plugins/woocommerce-advanced-product-labels/assets/admin/images/placeholder.png");
        $(".product-label img").css({
            width: "50px",
        });
        $("#_unique_label_image").val("");
        $("#_unique_label_image").val("http://wordpress.local/wp-content/plugins/woocommerce-advanced-product-labels/assets/admin/images/placeholder.png");
        $(".it_remove_image_button").hide();
        return false;
    });

    /**************Admin Panel's Setting Tab End Here****************/
    /*custom range*/
    var rangeSlider = function () {
        var slider = $(".range-slider"),
            range = $(".range-slider__range"),
            value = $(".range-slider__value");

        slider.each(function () {
            value.each(function () {
                var value = $(this).prev().attr("value");
                $(this).html(value);
            });

            range.on("input", function () {
                $(this).next(value).html(this.value);
            });
        });
    };

    rangeSlider();
    /*end custom range*/

    if ($(".color-picker").length > 0 && $.fn.wpColorPicker) {
        $(".color-picker").wpColorPicker();
    }

    $(document).on("click", ".wcbe-history-pagination-item", function () {
        $(".wcbe-history-pagination-loading").show();

        let filters = {
            operation: $("#wcbe-history-filter-operation").val(),
            author: $("#wcbe-history-filter-author").val(),
            fields: $("#wcbe-history-filter-fields").val(),
            date: {
                from: $("#wcbe-history-filter-date-from").val(),
                to: $("#wcbe-history-filter-date-to").val(),
            },
        };

        wcbeHistoryChangePage($(this).attr("data-index"), filters);
    });

    if ($("#wcbe-settings-show-only-filtered-variations").val() === "yes") {
        $("#wcbe-bulk-edit-show-variations").prop("checked", true).attr("disabled", "disabled");
    }

    $(document).on("click", '[data-target="#wcbe-float-side-modal-bulk-new-products"]', function () {
        if ($(this).attr("data-tabs-loaded") != "true") {
            wcbeBulkNewTabsInit();
        }
    });

    $(document).on("click", '[data-target="#wcbe-float-side-modal-bulk-edit"]', function () {
        if ($(this).attr("data-tabs-loaded") != "true") {
            wcbeBulkEditTabsInit();
        }
    });

    $(document).on("click", '[data-target="#wcbe-float-side-modal-filter"]', function () {
        if ($(this).attr("data-tabs-loaded") != "true") {
            wcbeFilterFormTabsInit();
        }
    });

    $(document).on("click", ".wcbe-products-table-load-more-variations", function () {
        $(this).closest("td").find(".wcbe-products-table-load-more-variations-loading").show();
        wcbeLoadMoreVariations($(this).attr("data-variable-id"), $(this).attr("data-page"));
    });

    $(document).on("click", '[data-target="#wcbe-float-side-modal-column-manager"]', function () {
        if ($(this).attr("data-loaded") != "true") {
            wcbeColumnManagerLoad();
        }
    });

    $(document).on("click", '[data-target="#wcbe-float-side-modal-filter-profiles"]', function () {
        if ($(this).attr("data-loaded") != "true") {
            wcbeFilterProfileLoad();
        }
    });

    $(document).on("click", '[data-target="#wcbe-float-side-modal-column-profiles"]', function () {
        if ($(this).attr("data-loaded") != "true") {
            wcbeColumnProfileLoad();
        }
    });

    $(document).on("click", ".wcbe-bulk-edit-variations", function () {
        // get product variations
        let productID = $("input.wcbe-check-item:visible:checkbox:checked");

        if (!productID.length) {
            swal({
                title: "Please select one product",
                type: "warning",
            });
            return false;
        }

        wcbeOpenFloatSideModal($(this).attr("data-target"));

        // reset fields
        $("#wcbe-variation-bulk-edit-attributes-added").html("");
        $("#wcbe-variation-bulk-edit-attributes").val("").change();
        $(".wcbe-variation-bulk-edit-individual-items").html("");
        $(".wcbe-variation-bulk-edit-current-items").html("");
        $("#wcbe-variation-single-delete-items").html("");
        $("#wcbe-variation-single-delete-variations").hide();
        $("#wcbe-variation-bulk-edit-do-bulk-variations").attr("disabled", "disabled");
        $("#wcbe-variation-bulk-edit-manual-add").attr("disabled", "disabled");
        $("#wcbe-variation-bulk-edit-generate").attr("disabled", "disabled");
        $("#wcbe-variations-multiple-products-delete-variation").show();
        $("#wcbe-variation-attaching-variable-id").val("").change();
        $("#wcbe-variation-attaching-get-variations").attr("disabled", "disabled");
        $("#wcbe-variations-attaching-product-variations").html("");

        // set sortable
        let variationCurrentItems = $(".wcbe-variation-bulk-edit-current-items");
        variationCurrentItems.sortable({
            handle: ".wcbe-variation-bulk-edit-current-item-sortable-btn",
            cancel: "",
        });
        variationCurrentItems.disableSelection();

        if (productID.length === 1) {
            $(".wcbe-variation-bulk-edit-loading").show();
            wcbeGetProductVariations(productID.val());
            $("#wcbe-variation-single-delete-variations").show();
            $("#wcbe-variations-multiple-products-delete-variation").hide();
            $("#wcbe-variation-attaching-variable-id").val(productID.val()).change();
            $("#wcbe-variation-attaching-get-variations").prop("disabled", false).trigger("click");
        }
    });

    $(document).on("change", "input:radio[name=create_variation_mode]", function () {
        if ($(this).attr("data-mode") === "all_combination") {
            $("#wcbe-variation-bulk-edit-individual").hide();
            $("#wcbe-variation-bulk-edit-generate").show();
        } else {
            $("#wcbe-variation-bulk-edit-generate").hide();
            $("#wcbe-variation-bulk-edit-individual").show();
        }
    });

    $(document).on("select2:select", ".wcbe-select2-ajax", function (e) {
        if ($(".wcbe-variation-bulk-edit-individual-items div[data-id=" + $(this).attr("id") + "]").length === 0) {
            $(".wcbe-variation-bulk-edit-individual-items").append(
                '<div data-id="' + $(this).attr("id") + '"><select class="wcbe-variation-bulk-edit-manual-item" data-attribute-name="' + $(this).attr("data-attribute-name") + '"></select></div>'
            );
        }
        $(".wcbe-variation-bulk-edit-individual-items div[data-id=" + $(this).attr("id") + "]")
            .find("select")
            .append('<option value="' + e.params.data.id + '">' + e.params.data.id + "</option>");
        $("#wcbe-variation-bulk-edit-manual-add").prop("disabled", false);
        $("#wcbe-variation-bulk-edit-generate").prop("disabled", false);
    });

    $(document).on("select2:unselect", ".wcbe-select2-ajax", function (e) {
        $(".wcbe-variation-bulk-edit-individual-items div[data-id=" + $(this).attr("id") + "]")
            .find("option[value=" + e.params.data.id + "]")
            .remove();
        if ($(".wcbe-variation-bulk-edit-attribute-item").find(".select2-selection__choice").length === 0) {
            $("#wcbe-variation-bulk-edit-manual-add").attr("disabled", "disabled");
            $("#wcbe-variation-bulk-edit-generate").attr("disabled", "disabled");
        }
        if ($(this).val() === null) {
            $("div[data-id=wcbe-variation-bulk-edit-attribute-item-" + $(this).attr("data-attribute-name") + "]").remove();
        }
    });

    $(document).on("change", "input:radio[name=delete_variation_mode]", function () {
        if ($(this).attr("data-mode") === "delete_all") {
            $("#wcbe-variation-delete-single-delete").hide();
            $("#wcbe-variation-delete-delete-all").show();
        } else {
            $("#wcbe-variation-delete-delete-all").hide();
            $("#wcbe-variation-delete-single-delete").show();
        }
    });

    $(document).on("click", "#wcbe-variation-delete-selected-variation", function () {
        let deleteType = "multiple_product";
        let productIds;
        let variations = [];
        let attributeName;
        let productsChecked = $("input.wcbe-check-item:visible:checkbox:checked[data-item-type=variable]");
        if (productsChecked.length > 0) {
            productIds = productsChecked
                .map(function () {
                    return $(this).val();
                })
                .get();

            $("#wcbe-variation-bulk-edit-delete-attributes-added select").each(function () {
                attributeName = "attribute_pa_" + encodeURIComponent($(this).attr("data-name"));
                attributeName = attributeName.toLowerCase();
                variations.push({
                    [attributeName]: $(this).val(),
                });
            });

            swal(
                {
                    title: wcbeTranslate.areYouSure,
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                    confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                    confirmButtonText: wcbeTranslate.iAmSure,
                    closeOnConfirm: true,
                },
                function (isConfirm) {
                    if (isConfirm === true) {
                        wcbeDeleteProductsVariations(productIds, deleteType, variations);
                    }
                }
            );
        } else {
            swal({
                title: variableProductRequired,
                type: "warning",
            });
        }
    });

    $(document).on("change", "#wcbe-top-nav-filters-go-to-page", function () {
        if (goToPageProcessing === false && $(this).val() != "") {
            goToPageProcessing = true;
            wcbeProductsFilter(wcbeGetCurrentFilterData(), "pagination", [], $(this).val());
        }
    });

    $(document).on("click", '[data-target="#wcbe-modal-regular-price"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-regular-price .wcbe-modal-regular-price-apply-button").attr("data-item-id", tdElement.attr("data-item-id"));
        $("#wcbe-modal-regular-price .wcbe-modal-item-title").html(tdElement.attr("data-item-title"));
    });

    $(document).on("click", '[data-target="#wcbe-modal-sale-price"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-sale-price .wcbe-modal-sale-price-apply-button").attr("data-item-id", tdElement.attr("data-item-id"));
        $("#wcbe-modal-sale-price .wcbe-modal-item-title").html(tdElement.attr("data-item-title"));
    });

    $(document).on("click", '[data-target="#wcbe-modal-select-author"]', function () {
        jQuery("#wcbe-modal-select-author-input").html("");
        jQuery(".wcbe-modal-select-author-loading").show();
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-select-author .wcbe-modal-select-author-apply-button").attr("data-item-id", tdElement.attr("data-item-id"));
        $("#wcbe-modal-select-author .wcbe-modal-item-title").html(tdElement.attr("data-item-title"));

        wcbeGetProductAuthor(tdElement.attr("data-item-id"));
    });

    $(document).on("click", ".wcbe-modal-select-author-apply-button", function () {
        let productId = $(this).attr("data-item-id");
        let productIds;
        let productData = [];

        if ($("#wcbe-bind-edit").prop("checked") === true) {
            productIds = wcbeGetProductsChecked();
        } else {
            productIds = [];
        }
        if ($.isArray(productIds)) {
            productIds.push(productId);
        }

        productData.push({
            name: "post_author",
            sub_name: "",
            type: "wp_posts_field",
            value: $("#wcbe-modal-select-author-input").val(),
        });

        wcbeProductEdit(productIds, productData);
    });

    $(document).on("click", '[data-target="#wcbe-modal-product-taxonomy"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-product-taxonomy .wcbe-modal-item-title").html(tdElement.attr("data-item-title"));
        $(".wcbe-inline-edit-taxonomy-save").attr("data-item-id", tdElement.attr("data-item-id")).attr("data-name", tdElement.attr("data-name"));
        $(".wcbe-inline-edit-add-new-taxonomy").attr("data-item-id", tdElement.attr("data-item-id")).attr("data-field", tdElement.attr("data-name"));
        wcbeGetProductTaxonomyTerms(tdElement.attr("data-item-id"), tdElement.attr("data-name"));
    });

    $(document).on("click", '[data-target="#wcbe-modal-product-attribute"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-product-attribute .wcbe-modal-item-title").html(tdElement.attr("data-item-title"));
        $(".wcbe-inline-edit-attribute-save").attr("data-item-id", tdElement.attr("data-item-id")).attr("data-name", tdElement.attr("data-name"));
        $(".wcbe-inline-edit-add-new-attribute").attr("data-item-id", tdElement.attr("data-item-id")).attr("data-field", tdElement.attr("data-name"));
        wcbeGetProductAttributeTerms(tdElement.attr("data-item-id"), tdElement.attr("data-name"));
    });

    $(document).on("click", '[data-target="#wcbe-modal-acf-multi-select"]', function () {
        let tdElement = $(this).closest("td");
        $("#wcbe-modal-acf-multi-select .wcbe-modal-item-title").html(tdElement.attr("data-item-title"));
        $("#wcbe-modal-acf-multi-select .wcbe-edit-action-with-button").attr("data-item-id", tdElement.attr("data-item-id")).attr("data-name", tdElement.attr("data-name"));
        wcbeGetAcfTaxonomyTerms(tdElement.attr("data-item-id"), tdElement.attr("data-name"));
    });

    $(document).on("click", ".wcbe-bulk-edit-show-variations-button", function () {
        if ($('#wcbe-bulk-edit-show-variations').prop("checked") === true) {
            $(this).removeClass('selected');
            $('#wcbe-bulk-edit-show-variations').prop("checked", false).change();
        } else {
            $(this).addClass('selected');
            $('#wcbe-bulk-edit-show-variations').prop("checked", true).change();
        }
    });

    $(document).on("click", ".wcbe-processing-loading-stop-button", function () {
        let $this = $(this);
        swal(
            {
                title: "Your changes have been applied to a number of rows. Do you want to stop the operation?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: wcbeTranslate.iAmSure,
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    $this.hide();
                    jQuery('.wcbe-processing-loading span[data-type="message"]').text("Stopping ...");
                    wcbeBackgroundProcessForceStop();
                }
            }
        );
    });

    $(document).on("click", ".wcbe-bulk-edit-form-schedule-bulk-edit", function () {
        let container = $('#wcbe-float-side-modal-bulk-edit .wcbe-tab-content-item[data-content="set_schedule"]');

        container
            .find(".wcbe-set-schedule-form .required:visible")
            .each(function () {
                if ($(this).val() != "") {
                    $(this).removeClass("error");
                } else {
                    $(this).addClass("error");
                }
            })
            .promise()
            .done(function () {
                if (container.find(".wcbe-set-schedule-form").find(".error").length) {
                    container.find('.wcbe-tab-item[data-content="set_schedule"]').trigger("click");
                    wcbeLoadingError("Please fill the required fields");
                    return;
                }

                wcbeLoadingStart();

                let editItems = wcbeGetBulkEditData();
                let productIds = wcbeGetProductsChecked();
                let filterItems = $.isArray(productIds) && productIds.length ? { product_ids: productIds } : wcbeGetCurrentFilterData();

                if (!editItems.length) {
                    wcbeLoadingError("Bulk edit form is empty !");
                    return;
                }

                let dates = wcbeScheduleGetDatesFromJobForm(container);

                setTimeout(function () {
                    $.ajax({
                        url: WCBE_DATA.ajax_url,
                        type: "post",
                        dataType: "json",
                        data: {
                            action: "wcbe_add_schedule_job",
                            nonce: WCBE_DATA.ajax_nonce,
                            label: container.find(".wcbe-set-schedule-name").val(),
                            description: container.find(".wcbe-set-schedule-description").val(),
                            run_at: container.find(".wcbe-set-schedule-run-at").val(),
                            run_for: container.find(".wcbe-set-schedule-run-for:visible").length ? container.find(".wcbe-set-schedule-run-for").val() : null,
                            dates: dates,
                            filter_items: filterItems,
                            edit_items: editItems,
                            stop_date: container.find(".wcbe-set-schedule-stop-date-time:visible").length ? container.find(".wcbe-set-schedule-stop-date-time").val() : null,
                            revert_date: container.find(".wcbe-set-schedule-revert-date-time:visible").length ? container.find(".wcbe-set-schedule-revert-date-time").val() : null,
                        },
                        success: function (response) {
                            if (response.success) {
                                if (container.find(".wcbe-set-schedule-run-at").val() === "now") {
                                    if (response.is_processing) {
                                        wcbeLoadingProcessingStart(WCBE_DATA.background_process.loading_messages.processing, true, { total: 0, completed: 0 });
                                        wcbeIsProcessing();
                                    } else {
                                        $(".wcbe-reload-table").trigger("click");
                                    }
                                } else {
                                    wcbeLoadingSuccess();
                                }

                                wcbeScheduleAwaitingCountUpdate(response.awaiting_count);
                            } else {
                                wcbeLoadingError(response.message && response.message != "" ? response.message : "Error !");
                            }
                        },
                        error: function () {
                            wcbeLoadingError();
                        },
                    });
                }, 250);
            });
    });

    wcbeGetDefaultFilterProfileProducts();
    wcbeSelect2UsersInit();
    wcbeSelect2ProductsInit();
    wcbeSelect2Init();
    wcbeBackgroundProcessingCheck();
});
