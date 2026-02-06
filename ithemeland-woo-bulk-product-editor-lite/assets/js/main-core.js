"use strict";

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
  $(document).on("click", ".wcbe-timepicker, .wcbe-datetimepicker, .wcbe-datepicker", function () {
    $(this).attr("data-val", $(this).val());
  });

  wcbeReInitDatePicker();
  wcbeReInitColorPicker();

  // Select2
  if ($.fn.select2) {
    let wcbeSelect2 = $(".wcbe-select2");
    if (wcbeSelect2.length) {
      wcbeSelect2.select2({
        placeholder: "Select ...",
      });
    }
  }

  $(document).on("click", ".wcbe-tabs-list li a.wcbe-tab-item", function (event) {
    if ($(this).attr("data-disabled") !== "true") {
      event.preventDefault();

      if ($(this).closest(".wcbe-tabs-list").attr("data-type") == "url") {
        window.location.hash = $(this).attr("data-content");
      }

      wcbeOpenTab($(this));
    }
  });

  // Modal
  $(document).on("click", '[data-toggle="modal"]', function () {
    wcbeOpenModal($(this).attr("data-target"));
  });

  $(document).on("click", '[data-toggle="modal-close"]', function () {
    wcbeCloseModal();
  });

  // Float side modal
  $(document).on("click", '[data-toggle="float-side-modal"]', function () {
    wcbeOpenFloatSideModal($(this).attr("data-target"));
  });

  $(document).on("click", '[data-toggle="float-side-modal-close"]', function () {
    if ($(".wcbe-float-side-modal:visible").length && $(".wcbe-float-side-modal:visible").hasClass("wcbe-float-side-modal-close-with-confirm")) {
      swal(
        {
          title: "Are you sure?",
          type: "warning",
          showCancelButton: true,
          cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
          confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
          confirmButtonText: wcbeTranslate.iAmSure,
          closeOnConfirm: true,
        },
        function (isConfirm) {
          if (isConfirm) {
            $(".wcbe-float-side-modal:visible").removeClass("wcbe-float-side-modal-close-with-confirm");
            wcbeCloseFloatSideModal();
          }
        }
      );
    } else {
      wcbeCloseFloatSideModal();
    }
  });

  $(document).on("keyup", function (e) {
    if (e.keyCode === 27) {
      if (jQuery(".wcbe-modal:visible").length > 0) {
        wcbeCloseModal();
      } else {
        if ($(".wcbe-float-side-modal:visible").length && $(".wcbe-float-side-modal:visible").hasClass("wcbe-float-side-modal-close-with-confirm")) {
          swal(
            {
              title:
                $(".wcbe-float-side-modal:visible").attr("data-confirm-message") && $(".wcbe-float-side-modal:visible").attr("data-confirm-message") != ""
                  ? $(".wcbe-float-side-modal:visible").attr("data-confirm-message")
                  : "Are you sure?",
              type: "warning",
              showCancelButton: true,
              cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
              confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
              confirmButtonText: wcbeTranslate.iAmSure,
              closeOnConfirm: true,
            },
            function (isConfirm) {
              if (isConfirm) {
                $(".wcbe-float-side-modal:visible").removeClass("wcbe-float-side-modal-close-with-confirm");
                wcbeCloseFloatSideModal();
              }
            }
          );
        } else {
          if (!$(".wcbe-float-side-modal:visible").hasClass("wcbe-disable-esc-for-close")) {
            wcbeCloseFloatSideModal();
          }
        }
      }

      $("[data-type=edit-mode]").each(function () {
        $(this).closest("span").html($(this).attr("data-val"));
      });

      if ($("#wcbe-filter-form-content").css("display") === "block") {
        $("#wcbe-bulk-edit-filter-form-close-button").trigger("click");
      }
    }
  });

  // Color Picker Style
  $(document).on("change", "input[type=color]", function () {
    this.parentNode.style.backgroundColor = this.value;
  });

  $(document).on("click", "#wcbe-full-screen", function () {
    if ($("#adminmenuback").css("display") === "block") {
      openFullscreen();
    } else {
      exitFullscreen();
    }
  });

  if (document.addEventListener) {
    document.addEventListener("fullscreenchange", wcbeFullscreenHandler, false);
    document.addEventListener("mozfullscreenchange", wcbeFullscreenHandler, false);
    document.addEventListener("MSFullscreenChange", wcbeFullscreenHandler, false);
    document.addEventListener("webkitfullscreenchange", wcbeFullscreenHandler, false);
  }

  $(document).on("click", ".wcbe-top-nav-duplicate-button", function () {
    let itemIds = $("input.wcbe-check-item:visible:checkbox:checked")
      .map(function () {
        if ($(this).attr("data-item-type") === "variation") {
          swal({
            title: "Duplicate for variations product is disabled !",
            type: "warning",
          });
        } else {
          return $(this).val();
        }
      })
      .get();

    if (!itemIds.length) {
      swal({
        title: $('input.wcbe-check-item[data-item-type="variation"]:visible:checkbox:checked') ? "Duplicate for variations product is disabled !" : "Please select one product",
        type: "warning",
      });
      return false;
    } else {
      wcbeOpenModal("#wcbe-modal-item-duplicate");
    }
  });

  // Select Items (Checkbox) in table
  $(document).on("change", ".wcbe-check-item-main", function () {
    let checkbox_items = $(".wcbe-check-item");
    if ($(this).prop("checked") === true) {
      checkbox_items.prop("checked", true);
      $("#wcbe-items-list tr").addClass("wcbe-tr-selected");
      checkbox_items.each(function () {
        $("#wcbe-export-items-selected").append("<input type='hidden' name='item_ids[]' value='" + $(this).val() + "'>");
      });
      wcbeShowSelectionTools();
      $("#wcbe-export-only-selected-items").prop("disabled", false);
    } else {
      checkbox_items.prop("checked", false);
      $("#wcbe-items-list tr").removeClass("wcbe-tr-selected");
      $("#wcbe-export-items-selected").html("");
      wcbeHideSelectionTools();
      $("#wcbe-export-only-selected-items").prop("disabled", true);
      $("#wcbe-export-all-items-in-table").prop("checked", true);
    }
  });

  $(document).on("change", ".wcbe-check-item", function () {
    if ($(this).prop("checked") === true) {
      $("#wcbe-export-items-selected").append("<input type='hidden' name='item_ids[]' value='" + $(this).val() + "'>");
      $(this).closest("tr").addClass("wcbe-tr-selected");
    } else {
      $("#wcbe-export-items-selected")
        .find("input[value=" + $(this).val() + "]")
        .remove();
      $(this).closest("tr").removeClass("wcbe-tr-selected");
    }

    wcbeCheckSelectAllStatus();

    // Disable and enable "Only Selected items" in "Import/Export"
    if ($(".wcbe-check-item:checkbox:checked").length > 0) {
      $("#wcbe-export-only-selected-items").prop("disabled", false);
      wcbeShowSelectionTools();
    } else {
      wcbeHideSelectionTools();
      $("#wcbe-export-only-selected-items").prop("disabled", true);
      $("#wcbe-export-all-items-in-table").prop("checked", true);
    }
  });

  $(document).on("click", "#wcbe-bulk-edit-unselect", function () {
    $("input.wcbe-check-item").prop("checked", false);
    $("input.wcbe-check-item-main").prop("checked", false);
    wcbeHideSelectionTools();
  });

  // Start "Column Profile"
  $(document).on("change", "#wcbe-column-profiles-choose", function () {
    let preset = $(this).val();
    $('.wcbe-column-profiles-fields input[type="checkbox"]').prop("checked", false);
    $("#wcbe-column-profile-select-all").prop("checked", false);
    $(".wcbe-column-profile-select-all span").text("Select All");
    $("#wcbe-column-profiles-apply").attr("data-preset-key");
    if (defaultPresets && $.inArray(preset, defaultPresets) === -1) {
      $("#wcbe-column-profiles-update-changes").show();
    } else {
      $("#wcbe-column-profiles-update-changes").hide();
    }

    if (columnPresetsFields && columnPresetsFields[preset]) {
      columnPresetsFields[preset].forEach(function (val) {
        $('.wcbe-column-profiles-fields input[type="checkbox"][value="' + val + '"]').prop("checked", true);
      });
    }
  });

  $(document).on("keyup", "#wcbe-column-profile-search", function () {
    let wcbeSearchFieldValue = $(this).val().toLowerCase().trim();
    $(".wcbe-column-profile-fields ul li").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(wcbeSearchFieldValue) > -1);
    });
  });

  $(document).on("change", "#wcbe-column-profile-select-all", function () {
    if ($(this).prop("checked") === true) {
      $(this).closest("label").find("span").text("Unselect");
      $(".wcbe-column-profile-fields input:checkbox:visible").prop("checked", true);
    } else {
      $(this).closest("label").find("span").text("Select All");
      $(".wcbe-column-profile-fields input:checkbox").prop("checked", false);
    }
    $(".wcbe-column-profile-save-dropdown").show();
  });
  // End "Column Profile"

  // Calculator for numeric TD
  $(document).on(
    {
      mouseenter: function () {
        $(this).children(".wcbe-calculator").show();
      },
      mouseleave: function () {
        $(this).children(".wcbe-calculator").hide();
      },
    },
    "td[data-content-type=regular_price], td[data-content-type=sale_price], td[data-content-type=numeric]"
  );

  // delete items button
  $(document).on("click", ".wcbe-bulk-edit-delete-item", function () {
    $(this).find(".wcbe-bulk-edit-delete-item-buttons").slideToggle(200);
  });

  $(document).on("change", ".wcbe-column-profile-fields input:checkbox", function () {
    $(".wcbe-column-profile-save-dropdown").show();
  });

  $(document).on("click", ".wcbe-column-profile-save-dropdown", function () {
    $(this).find(".wcbe-column-profile-save-dropdown-buttons").slideToggle(200);
  });

  $("#wp-admin-bar-root-default").append('<li id="wp-admin-bar-wcbe-col-view"></li>');

  $(document).on(
    {
      mouseenter: function () {
        $("#wp-admin-bar-wcbe-col-view").html(
          "#" + $(this).attr("data-item-id") + " | " + $(this).attr("data-item-title") + ' [<span class="wcbe-col-title">' + $(this).attr("data-col-title") + "</span>] "
        );
      },
      mouseleave: function () {
        $("#wp-admin-bar-wcbe-col-view").html("");
      },
    },
    "#wcbe-items-list td"
  );

  $(document).on("click", ".wcbe-open-uploader", function (e) {
    let target = $(this).attr("data-target");
    let element = $(this).closest("div");
    let type = $(this).attr("data-type");
    let mediaUploader;
    let wcbeNewImageElementID = $(this).attr("data-id");
    let wcbeProductID = $(this).attr("data-item-id");
    e.preventDefault();
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    if (type === "single") {
      mediaUploader = wp.media.frames.file_frame = wp.media({
        title: "Choose Image",
        button: {
          text: "Choose Image",
        },
        multiple: false,
      });
    } else {
      mediaUploader = wp.media.frames.file_frame = wp.media({
        title: "Choose Images",
        button: {
          text: "Choose Images",
        },
        multiple: true,
      });
    }

    mediaUploader.on("select", function () {
      let attachment = mediaUploader.state().get("selection").toJSON();
      if ($(target).length) {
        $(target).val(attachment[0].id);
        if ($(target + "-url").length) {
          $(target + "-url").val(attachment[0].url);
        }
        if ($(target + "-preview").length) {
          $(target + "-preview").html(
            '<div><img src="' + attachment[0].url + '" width="43" height="43" alt=""><button type="button" class="wcbe-bulk-edit-form-remove-image"><i class="wcbe-icon-x"></i></button></div>'
          );
        }
      } else {
        switch (target) {
          case "inline-file":
            $("#url-" + wcbeNewImageElementID).val(attachment[0].url);
            break;
          case "inline-file-custom-field":
            $("#wcbe-file-url").val(attachment[0].url);
            $("#wcbe-file-id").val(attachment[0].id);
            break;
          case "inline-edit":
            $("#" + wcbeNewImageElementID).val(attachment[0].url);
            $("#wcbe-modal-image button[data-item-id=" + wcbeProductID + "][data-button-type=save]")
              .attr("data-image-id", attachment[0].id)
              .attr("data-image-url", attachment[0].url)
              .attr("data-height-fixed", "false");
            $("[data-image-preview-id=" + wcbeNewImageElementID + "]")
              .html("<img src='" + attachment[0].url + "' alt='' />")
              .ready(function () {
                $('#wcbe-modal-image button[data-button-type="save"]').prop("disabled", false);
                setTimeout(function () {
                  wcbeFixModalHeight($("#wcbe-modal-image"));
                }, 150);
              });
            break;
          case "variations-inline-edit":
            $("#wcbe-variation-thumbnail-modal .wcbe-inline-image-preview").html("<img src='" + attachment[0].url + "' alt='' />");
            $('#wcbe-variation-thumbnail-modal .wcbe-variations-table-thumbnail-inline-edit-button[data-button-type="save"]')
              .attr("data-image-id", attachment[0].id)
              .attr("data-image-url", attachment[0].url);
            break;
          case "inline-edit-gallery":
            attachment.forEach(function (item) {
              $("#wcbe-modal-gallery-items").append(
                '<div class="wcbe-inline-edit-gallery-item"><img src="' + item.url + '" alt=""><input type="hidden" class="wcbe-inline-edit-gallery-image-ids" value="' + item.id + '"></div>'
              );
            });
            break;
          case "bulk-edit-image":
            element.find(".wcbe-bulk-edit-form-item-image").val(attachment[0].id);
            element
              .find(".wcbe-bulk-edit-form-item-image-preview")
              .html(
                '<div><img src="' + attachment[0].url + '" width="43" height="43" alt=""><button type="button" class="wcbe-bulk-edit-form-remove-image"><i class="wcbe-icon-x"></i></button></div>'
              );
            break;
          case "variations-bulk-actions-image":
            element.find(".wcbe-variations-bulk-actions-image").val(attachment[0].id);
            element
              .find(".wcbe-variations-bulk-actions-image-preview")
              .html(
                '<div><img src="' +
                  attachment[0].url +
                  '" width="43" height="43" alt=""><button type="button" class="wcbe-variations-bulk-actions-remove-image"><i class="wcbe-icon-x"></i></button></div>'
              );
            break;
          case "variations-bulk-actions-file":
            element.find(".wcbe-variation-bulk-actions-file-item-url-input").val(attachment[0].url);
            break;
          case "bulk-edit-file":
            element.find(".wcbe-bulk-edit-form-item-file").val(attachment[0].id);
            break;
          case "bulk-edit-gallery":
            attachment.forEach(function (item) {
              $(".wcbe-bulk-edit-form-item-gallery").append('<input type="hidden" value="' + item.id + '" data-field="value">');
              $(".wcbe-bulk-edit-form-item-gallery-preview").append(
                '<div><img src="' +
                  item.url +
                  '" width="43" height="43" alt=""><button type="button" data-id="' +
                  item.id +
                  '" class="wcbe-bulk-edit-form-remove-gallery-item"><i class="wcbe-icon-x"></i></button></div>'
              );
            });
            break;
        }
      }
    });
    mediaUploader.open();
  });

  $(document).on("click", ".wcbe-inline-edit-gallery-image-item-delete", function () {
    $(this).closest("div").remove();
  });

  $(document).on("change", ".wcbe-column-manager-check-all-fields-btn input:checkbox", function () {
    if ($(this).prop("checked")) {
      $(this).closest("label").find("span").addClass("selected").text("Unselect");
      $(".wcbe-column-manager-available-fields[data-action=" + $(this).closest("label").attr("data-action") + "] li:visible").each(function () {
        $(this).find("input:checkbox").prop("checked", true);
      });
    } else {
      $(this).closest("label").find("span").removeClass("selected").text("Select All");
      $(".wcbe-column-manager-available-fields[data-action=" + $(this).closest("label").attr("data-action") + "] li:visible input:checked").prop("checked", false);
    }
  });

  $(document).on("click", ".wcbe-column-manager-add-field", function () {
    let fieldName = [];
    let fieldLabel = [];
    let action = $(this).attr("data-action");
    let checked = $(".wcbe-column-manager-available-fields[data-action=" + action + "] input[data-type=field]:checkbox:checked");
    if (checked.length > 0) {
      $(".wcbe-column-manager-empty-text").hide();
      if (action === "new") {
        $(".wcbe-column-manager-added-fields-wrapper .wcbe-box-loading").show();
      } else {
        $("#wcbe-modal-column-manager-edit-preset .wcbe-box-loading").show();
      }
      checked.each(function (i) {
        fieldName[i] = $(this).attr("data-name");
        fieldLabel[i] = $(this).val();
      });
      wcbeColumnManagerAddField(fieldName, fieldLabel, action);
    }
  });

  $(".wcbe-column-manager-delete-preset").on("click", function () {
    var $this = $(this);
    $("#wcbe_column_manager_delete_preset_key").val($this.val());
    swal(
      {
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
        confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
        confirmButtonText: "Yes, I'm sure !",
        closeOnConfirm: true,
      },
      function (isConfirm) {
        if (isConfirm) {
          $("#wcbe-column-manager-delete-preset-form").submit();
        }
      }
    );
  });

  $(document).on("keyup", ".wcbe-column-manager-search-field", function () {
    let wcbeSearchFieldValue = $(this).val().toLowerCase().trim();
    $(".wcbe-column-manager-available-fields[data-action=" + $(this).attr("data-action") + "] ul li[data-added=false]").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(wcbeSearchFieldValue) > -1);
    });
  });

  $(document).on("click", ".wcbe-column-manager-remove-field", function () {
    $(".wcbe-column-manager-available-fields[data-action=" + $(this).attr("data-action") + "] li[data-name=" + $(this).attr("data-name") + "]")
      .attr("data-added", "false")
      .show();
    $(this).closest(".wcbe-column-manager-right-item").remove();
    if ($(".wcbe-column-manager-added-fields-wrapper .wcbe-column-manager-right-item").length < 1) {
      $(".wcbe-column-manager-empty-text").show();
    }
  });

  if ($.fn.sortable) {
    let wcbeColumnManagerFields = $(".wcbe-column-manager-added-fields .items");
    wcbeColumnManagerFields.sortable({
      handle: ".wcbe-column-manager-field-sortable-btn",
      cancel: "",
    });
    wcbeColumnManagerFields.disableSelection();

    let wcbeMetaFieldItems = $(".wcbe-meta-fields-right");
    wcbeMetaFieldItems.sortable({
      handle: ".wcbe-meta-field-item-sortable-btn",
      cancel: "",
    });
    wcbeMetaFieldItems.disableSelection();
  }

  $(document).on("click", "#wcbe-add-meta-field-manual", function () {
    $(".wcbe-meta-fields-empty-text").hide();
    let input = $("#wcbe-meta-fields-manual_key_name");
    wcbeAddMetaKeysManual(input.val().toLowerCase());
    input.val("");
  });

  $(document).on("click", "#wcbe-add-acf-meta-field", function () {
    let input = $("#wcbe-add-meta-fields-acf");
    if (input.val()) {
      $(".wcbe-meta-fields-empty-text").hide();
      wcbeAddACFMetaField(input.val(), input.find("option:selected").text(), input.find("option:selected").attr("data-type"));
      input.val("").change();
    }
  });

  $(document).on("click", ".wcbe-meta-field-remove", function () {
    $(this).closest(".wcbe-meta-fields-right-item").remove();
    if ($(".wcbe-meta-fields-right-item").length < 1) {
      $(".wcbe-meta-fields-empty-text").show();
    }
  });

  $(document).on("click", ".wcbe-history-delete-item", function () {
    $("#wcbe-history-clicked-id").attr("name", "delete").val($(this).val());
    swal(
      {
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
        confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
        confirmButtonText: "Yes, I'm sure !",
        closeOnConfirm: true,
      },
      function (isConfirm) {
        if (isConfirm) {
          $("#wcbe-history-items").submit();
        }
      }
    );
  });

  $(document).on("click", "#wcbe-history-clear-all-btn", function () {
    swal(
      {
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
        confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
        confirmButtonText: "Yes, I'm sure !",
        closeOnConfirm: true,
      },
      function (isConfirm) {
        if (isConfirm) {
          $("#wcbe-history-clear-all").submit();
        }
      }
    );
  });

  $(document).on("click", ".wcbe-history-revert-item", function () {
    $("#wcbe-history-clicked-id").attr("name", "revert").val($(this).val());
    swal(
      {
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
        confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
        confirmButtonText: "Yes, I'm sure !",
        closeOnConfirm: true,
      },
      function (isConfirm) {
        if (isConfirm) {
          $("#wcbe-history-items").submit();
        }
      }
    );
  });

  $(document).on("click", ".wcbe-modal", function (e) {
    if ($(e.target).hasClass("wcbe-modal") || $(e.target).hasClass("wcbe-modal-container") || $(e.target).hasClass("wcbe-modal-box")) {
      wcbeCloseModal();
    }
  });

  $(document).on("change", 'select[data-field="operator"]', function () {
    if ($(this).val() === "number_formula") {
      $(this).closest("div").find("input[type=number]").attr("type", "text");
    }
  });

  $(document).on("change", "#wcbe-filter-form-content [data-field=value], #wcbe-filter-form-content [data-field=from], #wcbe-filter-form-content [data-field=to]", function () {
    wcbeCheckFilterFormChanges();
  });

  $(document).on("change", "input[type=number][data-field=to]", function () {
    let from = $(this).closest(".wcbe-form-group").find("input[type=number][data-field=from]");
    if (parseFloat($(this).val()) < parseFloat(from.val())) {
      from.val("").addClass("wcbe-input-danger").focus();
    }
  });

  $(document).on("change", "input[type=number][data-field=from]", function () {
    let to = $(this).closest(".wcbe-form-group").find("input[type=number][data-field=to]");
    if (parseFloat($(this).val()) > parseFloat(to.val())) {
      $(this).val("").addClass("wcbe-input-danger");
    } else {
      $(this).removeClass("wcbe-input-danger");
    }
  });

  $(document).on("change", "#wcbe-switcher", function () {
    wcbeLoadingStart();
    $("#wcbe-switcher-form").submit();
  });

  $(document).on("click", 'span[data-target="#wcbe-modal-image"]', function () {
    let tdElement = $(this).closest("td");
    let modal = $("#wcbe-modal-image");
    let col_title = tdElement.attr("data-col-title");
    let id = $(this).attr("data-id");
    let image_id = $(this).attr("data-image-id");
    let item_id = tdElement.attr("data-item-id");
    let full_size_url = $(this).attr("data-full-image-src");
    let field = tdElement.attr("data-field");
    let field_type = tdElement.attr("data-field-type");

    $("#wcbe-modal-image-item-title").text(col_title);
    modal.find(".wcbe-open-uploader").attr("data-id", id).attr("data-item-id", item_id);
    modal
      .find(".wcbe-inline-image-preview")
      .attr("data-image-preview-id", id)
      .html('<img src="' + full_size_url + '" />')
      .ready(function () {
        modal.find(".wcbe-inline-image-preview img").load(function () {
          wcbeFixModalHeight(modal);
        });
      });
    modal.find(".wcbe-image-preview-hidden-input").attr("id", id);
    modal
      .find('button[data-button-type="save"]')
      .attr("data-item-id", item_id)
      .attr("data-field", field)
      .attr("data-image-url", full_size_url)
      .attr("data-image-id", image_id)
      .attr("data-field-type", field_type)
      .attr("data-name", tdElement.attr("data-name"))
      .attr("data-update-type", tdElement.attr("data-update-type"));
    modal
      .find('button[data-button-type="remove"]')
      .attr("data-item-id", item_id)
      .attr("data-field", field)
      .attr("data-field-type", field_type)
      .attr("data-name", tdElement.attr("data-name"))
      .attr("data-update-type", tdElement.attr("data-update-type"));
    modal.find('button[data-button-type="save"]').prop("disabled", true);

    if (image_id == "0") {
      modal.find('button[data-button-type="remove"]').prop("disabled", true);
    } else {
      modal.find('button[data-button-type="remove"]').prop("disabled", false);
    }
  });

  $(document).on("click", "#wcbe-modal-file-clear", function () {
    let modal = $("#wcbe-modal-file");
    modal.find("#wcbe-file-id").val(0).change();
    modal.find("#wcbe-file-url").val("").change();
  });

  $(document).on("click", ".wcbe-sub-tab-title", function () {
    $(this).closest(".wcbe-sub-tab-titles").find(".wcbe-sub-tab-title").removeClass("active");
    $(this).addClass("active");

    $(this).closest("div").find(".wcbe-sub-tab-content").hide();
    $(this)
      .closest("div")
      .find('.wcbe-sub-tab-content[data-content="' + $(this).attr("data-content") + '"]')
      .show();
  });

  if ($(".wcbe-sub-tab-titles").length > 0) {
    $(".wcbe-sub-tab-titles").each(function () {
      $(this).find(".wcbe-sub-tab-title").first().trigger("click");
    });
  }

  $(document).on("mouseenter", ".wcbe-thumbnail", function () {
    let position = $(this).offset();
    let imageHeight = $(this).find("img").first().height();
    let top = position.top - imageHeight > $("#wpadminbar").offset().top ? position.top - imageHeight : position.top + 15;

    $(".wcbe-thumbnail-hover-box")
      .css({
        top: top,
        left: position.left - 100,
        display: "block",
        height: imageHeight,
      })
      .html($(this).find(".wcbe-original-thumbnail").clone());
  });

  $(document).on("mouseleave", ".wcbe-thumbnail", function () {
    $(".wcbe-thumbnail-hover-box").hide();
  });

  setTimeout(function () {
    $("#wcbe-column-profiles-choose").trigger("change");
  }, 500);

  $(document).on("click", ".wcbe-filter-form-action", function () {
    wcbeFilterFormClose();
  });

  $(document).on("click", "#wcbe-license-renew-button", function () {
    $(this).closest("#wcbe-license").find(".wcbe-license-form").slideDown();
  });

  $(document).on("click", "#wcbe-license-form-cancel", function () {
    $(this).closest("#wcbe-license").find(".wcbe-license-form").slideUp();
  });

  $(document).on("click", "#wcbe-license-deactivate-button", function () {
    swal(
      {
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
        confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
        confirmButtonText: "Yes, I'm sure !",
        closeOnConfirm: true,
      },
      function (isConfirm) {
        if (isConfirm) {
          $("#wcbe-license-deactivation-form").submit();
        }
      }
    );
  });

  wcbeSetTipsyTooltip();

  $(window).on("resize", function () {
    wcbeDataTableFixSize();
  });

  $(document).on("click", "body", function (e) {
    if (!$(e.target).hasClass("wcbe-status-filter-button") && $(e.target).closest(".wcbe-status-filter-button").length == 0) {
      $(".wcbe-top-nav-status-filter").hide();
    }

    if (
      !$(e.target).hasClass("wcbe-table-item-selector-container") &&
      !$(e.target).closest(".wcbe-table-item-selector-container").length &&
      $(".wcbe-table-item-selector-container ul:visible").length
    ) {
      $(".wcbe-table-item-selector-container ul:visible").fadeOut(50);
    }

    if (!$(e.target).hasClass("wcbe-quick-filter") && $(e.target).closest(".wcbe-quick-filter").length == 0) {
      $(".wcbe-top-nav-filters").hide();
    }

    if (!$(e.target).hasClass("wcbe-post-type-switcher") && $(e.target).closest(".wcbe-post-type-switcher").length == 0) {
      $(".wcbe-top-nav-filters-switcher").hide();
    }

    if (
      !$(e.target).hasClass("wcbe-float-side-modal") &&
      !$(e.target).closest(".wcbe-float-side-modal-box").length &&
      !$(".sweet-overlay:visible").length &&
      !$(".wcbe-modal:visible").length &&
      $(e.target).attr("data-toggle") != "float-side-modal" &&
      !$(e.target).closest(".select2-container").length &&
      !$(e.target).is("i") &&
      !$(e.target).hasClass("wcbe-bulk-edit-form-remove-image") &&
      !$(e.target).hasClass("wcbe-bulk-edit-custom-field-file-remove-item") &&
      !$(e.target).closest(".media-modal").length &&
      !$(e.target).closest(".sweet-alert").length &&
      !$(e.target).closest('[data-toggle="float-side-modal"]').length &&
      !$(e.target).closest('[data-toggle="float-side-modal-after-confirm"]').length
    ) {
      if ($(".wcbe-float-side-modal:visible").length && $(".wcbe-float-side-modal:visible").hasClass("wcbe-float-side-modal-close-with-confirm")) {
        swal(
          {
            title:
              $(".wcbe-float-side-modal:visible").attr("data-confirm-message") && $(".wcbe-float-side-modal:visible").attr("data-confirm-message") != ""
                ? $(".wcbe-float-side-modal:visible").attr("data-confirm-message")
                : "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
            confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
            confirmButtonText: wcbeTranslate.iAmSure,
            closeOnConfirm: true,
          },
          function (isConfirm) {
            if (isConfirm) {
              $(".wcbe-float-side-modal:visible").removeClass("wcbe-float-side-modal-close-with-confirm");
              wcbeCloseFloatSideModal();
            }
          }
        );
      } else {
        wcbeCloseFloatSideModal();
      }
    }
  });

  $(document).on("click", ".wcbe-status-filter-button", function () {
    $(this).closest(".wcbe-status-filter-container").find(".wcbe-top-nav-status-filter").toggle();
  });

  $(document).on("click", ".wcbe-quick-filter > a", function (e) {
    if (!$(e.target).closest(".wcbe-top-nav-filters").length) {
      $(".wcbe-top-nav-filters").slideToggle(150);
    }
  });
  $(document).on("click", ".wcbe-post-type-switcher > a", function (e) {
    if (!$(e.target).closest(".wcbe-top-nav-filters-switcher").length) {
      $(".wcbe-top-nav-filters-switcher").slideToggle(150);
    }
  });

  $(document).on("click", ".wcbe-bind-edit-switch", function () {
    if ($("#wcbe-bind-edit").prop("checked") === true) {
      $("#wcbe-bind-edit").prop("checked", false);
      $(this).removeClass("active");
    } else {
      $("#wcbe-bind-edit").prop("checked", true);
      $(this).addClass("active");
    }
  });

  if ($("#wcbe-bind-edit").prop("checked") === true) {
    $(".wcbe-bind-edit-switch").addClass("active");
  } else {
    $(".wcbe-bind-edit-switch").removeClass("active");
  }

  if ($(".wcbe-flush-message").length) {
    setTimeout(function () {
      $(".wcbe-flush-message").slideUp();
    }, 3000);
  }

  $(document).on("input", "#wcbe-top-nav-filters-go-to-page", function () {
    if ($(this).val() == "") {
      return;
    }

    if (parseInt($(this).val()) < parseInt($(this).attr("min"))) {
      $(this).val($(this).attr("min"));
    }

    if (parseInt($(this).val()) > parseInt($(this).attr("max"))) {
      $(this).val($(this).attr("max"));
    }
  });

  $(document).on("click", ".wcbe-table-item-selector", function () {
    if ($(this).find("ul:visible").length) {
      $(this).find("ul").fadeOut(50);
    } else {
      $(this).find("ul").fadeIn(50);
    }
  });

  $(document).on("click", ".wcbe-table-item-selector label", function () {
    $(this).find("ul").fadeOut(50);
  });

  $(document).on("change", ".wcbe-table-item-selector label input:checkbox", function () {
    $(".wcbe-table-item-selector-checkbox").prop("checked", $(this).prop("checked"));

    if ($(this).val() == "visible") {
      $('.wcbe-check-item-main[value="all"]').prop("checked", false);
    } else {
      $('.wcbe-check-item-main[value="visible"]').prop("checked", false);
    }
  });

  setTimeout(function () {
    if ($("#wcbe-quick-search-reset").css("display") == "none") {
      $("li.wcbe-quick-filter a").removeClass("active");
    } else {
      $("li.wcbe-quick-filter a").addClass("active");
    }
  }, 150);

  wcbeDataTableFixSize();
});
