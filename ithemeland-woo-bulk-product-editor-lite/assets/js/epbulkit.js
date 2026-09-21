var wcbeWpEditorSettings = {
    mediaButtons: true,
    tinymce: {
        branding: false,
        theme: "modern",
        skin: "lightgray",
        language: "en",
        formats: {
            alignleft: [
                { selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: { textAlign: "left" } },
                { selector: "img,table,dl.wp-caption", classes: "alignleft" },
            ],
            aligncenter: [
                { selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: { textAlign: "center" } },
                { selector: "img,table,dl.wp-caption", classes: "aligncenter" },
            ],
            alignright: [
                { selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: { textAlign: "right" } },
                { selector: "img,table,dl.wp-caption", classes: "alignright" },
            ],
            strikethrough: { inline: "del" },
        },
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        browser_spellcheck: true,
        fix_list_elements: true,
        entities: "38,amp,60,lt,62,gt",
        entity_encoding: "raw",
        keep_styles: false,
        paste_webkit_styles: "font-weight font-style color",
        preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
        end_container_on_empty_block: true,
        wpeditimage_disable_captions: false,
        wpeditimage_html5_captions: true,
        plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
        menubar: false,
        wpautop: true,
        indent: false,
        resize: true,
        theme_advanced_resizing: true,
        theme_advanced_resize_horizontal: false,
        statusbar: true,
        toolbar1: "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_adv",
        toolbar2: "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
        toolbar3: "",
        toolbar4: "",
        tabfocus_elements: ":prev,:next",
    },
    quicktags: {
        buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,more,close",
    },
};

jQuery(document).ready(function ($) {
    "use strict";

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

    // Select2
    if ($.fn.select2) {
        $('.wcbe-tabs-list[data-content-id="wcbe-main-tabs-contents"] a[href="#"]').attr('data-toggle', '');
        let wcbeSelect2 = $(".wcbe-select2");
        if (wcbeSelect2.length) {
            wcbeSelect2.select2({
                placeholder: "Select ...",
            });
        }
    }
});