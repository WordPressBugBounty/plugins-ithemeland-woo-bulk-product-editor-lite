"use strict";

var wcbeOpenFullScreenIcon = '<i class="wcbe-icon-enlarge"></i>';
var wcbeCloseFullScreenIcon = '<i class="wcbe-icon-shrink"></i>';

function wcbeGetUrlParameter(sParam) {
  var sPageURL = window.location.search.substring(1),
    sURLVariables = sPageURL.split("&"),
    sParameterName,
    i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split("=");

    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
    }
  }
  return false;
}

function wcbeRemoveParamFromURL(url, param) {
  const [path, searchParams] = url.split("?");
  const newSearchParams = searchParams
    ?.split("&")
    .filter((p) => !(p === param || p.startsWith(`${param}=`)))
    .join("&");
  return newSearchParams ? `${path}?${newSearchParams}` : path;
}

function openFullscreen() {
  if (document.documentElement.requestFullscreen) {
    document.documentElement.requestFullscreen();
  } else if (document.documentElement.webkitRequestFullscreen) {
    document.documentElement.webkitRequestFullscreen();
  } else if (document.documentElement.msRequestFullscreen) {
    document.documentElement.msRequestFullscreen();
  }
}

function wcbeDataTableFixSize() {
  if (jQuery("html").attr("dir") == "rtl") {
    jQuery("#wcbe-main").css({
      top: jQuery("#wpadminbar").height() + "px",
      "padding-right": jQuery("#adminmenu:visible").length ? jQuery("#adminmenu").width() + "px" : 0,
    });
  } else {
    jQuery("#wcbe-main").css({
      top: jQuery("#wpadminbar").height() + "px",
      "padding-left": jQuery("#adminmenu:visible").length ? jQuery("#adminmenu").width() + "px" : 0,
    });
  }

  jQuery("#wcbe-loading").css({
    top: jQuery("#wpadminbar").height() + "px",
  });

  let height = parseInt(jQuery(window).height()) - parseInt(jQuery("#wcbe-header").height() + 85);

  jQuery(".wcbe-table").css({
    "max-height": height + "px",
  });
}

function exitFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) {
    document.webkitExitFullscreen();
  }
}

function wcbeFullscreenHandler() {
  if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
    jQuery("#wcbe-full-screen").html(wcbeOpenFullScreenIcon).attr("title", "Full screen");
    jQuery("#adminmenuback, #adminmenuwrap").show();
    jQuery("#wpcontent, #wpfooter").css({ "margin-left": "160px" });
    jQuery(".wcbe-processing-loading").css({ width: "calc(100% - 160px)" });
  } else {
    jQuery("#wcbe-full-screen").html(wcbeCloseFullScreenIcon).attr("title", "Exit Full screen");
    jQuery("#adminmenuback, #adminmenuwrap").hide();
    jQuery("#wpcontent, #wpfooter").css({ "margin-left": 0 });
    jQuery(".wcbe-processing-loading").css({ width: "100%" });
  }

  wcbeDataTableFixSize();
}

function wcbeOpenTab(item) {
  let wcbeTabItem = item;
  let wcbeParentContent = wcbeTabItem.closest(".wcbe-tabs-list");
  let wcbeParentContentID = wcbeParentContent.attr("data-content-id");
  let wcbeDataBox = wcbeTabItem.attr("data-content");
  wcbeParentContent.find("li a.selected").removeClass("selected");
  if (wcbeTabItem.closest(".wcbe-sub-tab").length > 0) {
    wcbeTabItem.closest("li.wcbe-has-sub-tab").find("a").first().addClass("selected");
  } else {
    wcbeTabItem.addClass("selected");
  }

  if (item.closest(".wcbe-tabs-list").attr("data-content-id") && item.closest(".wcbe-tabs-list").attr("data-content-id") == "wcbe-main-tabs-contents") {
    jQuery('.wcbe-tabs-list[data-content-id="wcbe-main-tabs-contents"] li[data-depend] a').not(".wcbe-tab-item").addClass("disabled");
    jQuery('.wcbe-tabs-list[data-content-id="wcbe-main-tabs-contents"] li[data-depend="' + wcbeDataBox + '"] a').removeClass("disabled");
  }

  jQuery("#" + wcbeParentContentID)
    .children("div.selected")
    .removeClass("selected");
  jQuery("#" + wcbeParentContentID + " div[data-content=" + wcbeDataBox + "]").addClass("selected");

  if (item.attr("data-type") === "main-tab") {
    wcbeFilterFormClose();
  }
}

function wcbeFixModalHeight(modal) {
  let footerHeight = 0;
  let search = 0;
  let contentHeight = parseInt(modal.find(".wcbe-modal-content").height());
  let titleHeight = parseInt(modal.find(".wcbe-modal-title").height());
  if (modal.find(".wcbe-modal-footer").length > 0) {
    footerHeight = parseInt(modal.find(".wcbe-modal-footer").height()) + 20;
  }

  if (modal.find(".wcbe-modal-top-search").length > 0) {
    search = parseInt(parseInt(modal.find(".wcbe-modal-top-search").height()) + 14);
  }

  let modalMargin = parseInt((parseInt(jQuery("body").height()) * 20) / 100);
  let bodyHeight = modal.find(".wcbe-modal-body-content").length ? parseInt(parseInt(modal.find(".wcbe-modal-body-content").height()) + 30) : contentHeight;
  let bodyMaxHeight = parseInt(jQuery("body").height()) - (titleHeight + search + footerHeight + modalMargin);

  modal.find(".wcbe-modal-content").css({
    height: parseInt(titleHeight + search + footerHeight + bodyHeight) + "px",
  });
  modal.find(".wcbe-modal-body").css({
    height: parseInt(bodyHeight + search) + "px",
    "max-height": parseInt(bodyMaxHeight + search) + "px",
  });
  modal.find(".wcbe-modal-box").css({
    height: parseInt(titleHeight + search + footerHeight + bodyHeight) + "px",
  });
  modal.attr("data-height-fixed", "true");
}

function wcbeOpenFloatSideModal(targetId) {
  let modal = jQuery(targetId);
  modal.fadeIn(20);
  modal.find(".wcbe-float-side-modal-box").animate(
    {
      right: 0,
    },
    180
  );
}

function wcbeCloseFloatSideModal() {
  // jQuery('body').removeClass('_winvoice-modal-open');
  // jQuery('._winvoice-modal-backdrop').remove();

  jQuery(".wcbe-float-side-modal-box").animate(
    {
      right: "-80%",
    },
    180
  );
  jQuery(".wcbe-float-side-modal").fadeOut(200);
}

function wcbeCloseModal() {
  jQuery("body").removeClass("_winvoice-modal-open");
  jQuery("._winvoice-modal-backdrop").remove();

  let lastModalOpened = jQuery("#wcbe-last-modal-opened");
  let modal = jQuery(lastModalOpened.val());
  if (lastModalOpened.val() !== "") {
    modal.find(" .wcbe-modal-box").fadeOut();
    modal.fadeOut();
    lastModalOpened.val("");
  } else {
    let lastModal = jQuery(".wcbe-modal:visible").last();
    lastModal.find(".wcbe-modal-box").fadeOut();
    lastModal.fadeOut();
  }

  setTimeout(function () {
    modal.find(".wcbe-modal-box").css({
      height: "auto",
      "max-height": "80%",
    });
    modal.find(".wcbe-modal-body").css({
      height: "auto",
      "max-height": "90%",
    });
    modal.find(".wcbe-modal-content").css({
      height: "auto",
      "max-height": "92%",
    });
  }, 400);
}

function wcbeOpenModal(targetId) {
  let modal = jQuery(targetId);
  modal.fadeIn();
  modal.find(".wcbe-modal-box").fadeIn();
  jQuery("#wcbe-last-modal-opened").val(targetId);

  // set height for modal body
  setTimeout(function () {
    wcbeFixModalHeight(modal);
  }, 150);
}

function wcbeReInitColorPicker() {
  if (jQuery(".wcbe-color-picker").length > 0) {
    jQuery(".wcbe-color-picker").wpColorPicker();
  }
  if (jQuery(".wcbe-color-picker-field").length > 0) {
    jQuery(".wcbe-color-picker-field").wpColorPicker();
  }
}

function wcbeReInitDatePicker() {
  if (jQuery.fn.datetimepicker) {
    jQuery(".wcbe-datepicker-with-dash").datetimepicker("destroy");
    jQuery(".wcbe-datepicker").datetimepicker("destroy");
    jQuery(".wcbe-timepicker").datetimepicker("destroy");
    jQuery(".wcbe-datetimepicker").datetimepicker("destroy");

    jQuery(".wcbe-datepicker").datetimepicker({
      timepicker: false,
      format: "Y/m/d",
      scrollMonth: false,
      scrollInput: false,
    });

    jQuery(".wcbe-datepicker-with-dash").datetimepicker({
      timepicker: false,
      format: "Y-m-d",
      scrollMonth: false,
      scrollInput: false,
    });

    jQuery(".wcbe-timepicker").datetimepicker({
      datepicker: false,
      format: "H:i",
      scrollMonth: false,
      scrollInput: false,
    });

    jQuery(".wcbe-datetimepicker").datetimepicker({
      format: "Y/m/d H:i",
      scrollMonth: false,
      scrollInput: false,
    });
  }
}

function wcbePaginationLoadingStart() {
  jQuery(".wcbe-pagination-loading").show();
}

function wcbePaginationLoadingEnd() {
  jQuery(".wcbe-pagination-loading").hide();
}

function wcbeLoadingStart() {
  jQuery("#wcbe-loading").removeClass("wcbe-loading-error").removeClass("wcbe-loading-success").text("Loading ...").slideDown(300);
}

function wcbeLoadingSuccess(message = "Success !") {
  jQuery("#wcbe-loading").removeClass("wcbe-loading-error").addClass("wcbe-loading-success").text(message).delay(1500).slideUp(200);
}

function wcbeLoadingProcessingStart(message = "Processing ...", stop_button = true, tasks = {}) {
  jQuery("#wcbe-loading").hide();
  jQuery("#wcbe-processing-loading").find('[data-type="loading"]').show();
  if (tasks.total) {
    setTimeout(function () {
      jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').show();
      jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').find('[data-type="total"]').text(tasks.total);
      jQuery("#wcbe-processing-loading")
        .find('[data-type="tasks"]')
        .find('[data-type="completed"]')
        .text(tasks.completed > 0 ? "+" + tasks.completed : tasks.completed);
    }, 10);
  } else {
    jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').hide();
  }
  jQuery("#wcbe-processing-loading").find('[data-type="result_icon"]').hide();
  if (stop_button === true) {
    jQuery("#wcbe-processing-loading").find(".wcbe-processing-loading-stop-button").show();
  } else {
    jQuery("#wcbe-processing-loading").find(".wcbe-processing-loading-stop-button").hide();
  }
  jQuery("#wcbe-processing-loading")
    .find('[data-type="message"]')
    .html(message)
    .ready(function () {
      jQuery("#wcbe-processing-loading").find('[data-type="message"]').show();
      jQuery("#wcbe-processing-loading").fadeIn(150);
    });
}

function wcbeLoadingProcessingPrepare(total_tasks = 0) {
  jQuery("#wcbe-loading").hide();
  jQuery("#wcbe-processing-loading").find(".wcbe-processing-loading-stop-button").hide();
  jQuery("#wcbe-processing-loading").find('[data-type="result_icon"]').hide();
  jQuery("#wcbe-processing-loading").find('[data-type="loading"]').show();
  jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').find('[data-type="total"]').text(0);
  jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').find('[data-type="completed"]').text(0);
  if (total_tasks > 0) {
    jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').show();
    jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').find('[data-type="total"]').text(total_tasks);
    jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').find('[data-type="completed"]').text(0);
  } else {
    jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').hide();
  }
  jQuery("#wcbe-processing-loading")
    .find('[data-type="message"]')
    .html("Your operation is being prepared. Please do not close the current tab.")
    .ready(function () {
      jQuery("#wcbe-processing-loading").find('[data-type="message"]').show();
      jQuery("#wcbe-processing-loading").fadeIn(150);
    });
}

function wcbeCheckSelectAllStatus() {
  if (parseInt(jQuery("input.wcbe-check-item:visible:checkbox:checked").length) === parseInt(jQuery("input.wcbe-check-item:visible:checkbox").length)) {
    if (jQuery('.wcbe-check-item-main[value="all"]').prop("checked") === true) {
      jQuery('.wcbe-check-item-main[value="all"]').prop("checked", true);
    } else {
      jQuery('.wcbe-check-item-main[value="visible"]').prop("checked", true);
    }
    jQuery(".wcbe-table-item-selector-checkbox").prop("checked", true);
  } else {
    jQuery(".wcbe-check-item-main").prop("checked", false);
    jQuery(".wcbe-table-item-selector-checkbox").prop("checked", false);
  }
}

function wcbeLoadingProcessingSuccess(message = "Success") {
  jQuery("#wcbe-processing-loading").find('[data-type="loading"]').hide();
  jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').hide();
  jQuery("#wcbe-processing-loading").find('[data-type="result_icon"]').show();
  jQuery("#wcbe-processing-loading").find(".wcbe-processing-loading-stop-button").hide();
  jQuery("#wcbe-processing-loading").find('[data-type="result_icon"] i').attr("class", "wcbe-icon-check-circle");
  jQuery("#wcbe-processing-loading")
    .find('[data-type="message"]')
    .html(message)
    .ready(function () {
      jQuery("#wcbe-processing-loading").find('[data-type="message"]').show();
      jQuery("#wcbe-processing-loading").delay(2000).fadeOut(150);
    });
}

function wcbeLoadingProcessingComplete(message = "Your changes have been applied", icon = "wcbe-icon-check-circle") {
  jQuery("#wcbe-processing-loading").find('[data-type="loading"]').hide();
  jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').hide();
  jQuery("#wcbe-processing-loading").find('[data-type="result_icon"]').show();
  jQuery("#wcbe-processing-loading").find(".wcbe-processing-loading-stop-button").hide();
  jQuery("#wcbe-processing-loading").find('[data-type="result_icon"] i').attr("class", icon);
  jQuery("#wcbe-processing-loading")
    .find('[data-type="message"]')
    .html(message)
    .ready(function () {
      jQuery("#wcbe-processing-loading").find('[data-type="message"]').show();
      jQuery("#wcbe-processing-loading").delay(3000).fadeOut(150);
    });
}

function wcbeLoadingProcessingError(message = "Error") {
  jQuery("#wcbe-processing-loading").find('[data-type="loading"]').hide();
  jQuery("#wcbe-processing-loading").find('[data-type="tasks"]').hide();
  jQuery("#wcbe-processing-loading").find(".wcbe-processing-loading-stop-button").hide();
  jQuery("#wcbe-processing-loading").find('[data-type="result_icon"]').show();
  jQuery("#wcbe-processing-loading").find('[data-type="result_icon"] i').attr("class", "wcbe-icon-x");
  jQuery("#wcbe-processing-loading")
    .find('[data-type="message"]')
    .html(message)
    .ready(function () {
      jQuery("#wcbe-processing-loading").find('[data-type="message"]').show();
      jQuery("#wcbe-processing-loading").delay(2000).fadeOut(150);
    });
}

function wcbeLoadingError(message = "Error !") {
  jQuery("#wcbe-loading").removeClass("wcbe-loading-success").removeClass("wcbe-loading-processing").addClass("wcbe-loading-error").text(message).delay(1500).slideUp(200);
}

function wcbeSetColorPickerTitle() {
  jQuery(".wcbe-column-manager-right-item .wp-picker-container").each(function () {
    let title = jQuery(this).find(".wcbe-column-manager-color-field input").attr("title");
    jQuery(this).attr("title", title);
    wcbeSetTipsyTooltip();
  });
}

function wcbeFilterFormClose() {
  if (jQuery("#wcbe-filter-form-content").attr("data-visibility") === "visible") {
    jQuery(".wcbe-filter-form-icon").addClass("wcbe-icon-chevron-down").removeClass("wcbe-icon-chevron-up");
    jQuery("#wcbe-filter-form-content").slideUp(200).attr("data-visibility", "hidden");
  }
}

function wcbeSetTipsyTooltip() {
  jQuery("[title]").tipsy({
    html: true,
    arrowWidth: 10, //arrow css border-width * 2, default is 5 * 2
    attr: "data-tipsy",
    cls: null,
    duration: 150,
    offset: 7,
    position: "top-center",
    trigger: "hover",
    onShow: null,
    onHide: null,
  });
}

function wcbeCheckUndoRedoStatus(reverted, history) {
  var isLiteVersion = jQuery("#wcbe-bulk-edit-undo").hasClass("wcbe-lite-version");

  if (isLiteVersion) {
    return;
  }

  if (reverted) {
    wcbeEnableRedo();
  } else {
    wcbeDisableRedo();
  }
  if (history) {
    wcbeEnableUndo();
  } else {
    wcbeDisableUndo();
  }
}

function wcbeDisableUndo() {
  jQuery("#wcbe-bulk-edit-undo").prop("disabled", true);
}

function wcbeEnableUndo() {
  jQuery("#wcbe-bulk-edit-undo").prop("disabled", false);
}

function wcbeDisableRedo() {
  jQuery("#wcbe-bulk-edit-redo").prop("disabled", true);
}

function wcbeEnableRedo() {
  jQuery("#wcbe-bulk-edit-redo").prop("disabled", false);
}

function wcbeHideSelectionTools() {
  jQuery(".wcbe-bulk-edit-form-selection-tools").hide();
  jQuery("#wcbe-bulk-edit-trash-restore").hide();
}

function wcbeShowSelectionTools() {
  jQuery(".wcbe-bulk-edit-form-selection-tools").show();
  jQuery("#wcbe-bulk-edit-trash-restore").show();
}

function wcbeSetColorPickerTitle() {
  jQuery(".wcbe-column-manager-right-item .wp-picker-container").each(function () {
    let title = jQuery(this).find(".wcbe-column-manager-color-field input").attr("title");
    jQuery(this).attr("title", title);
    wcbeSetTipsyTooltip();
  });
}

function wcbeColumnManagerAddField(fieldName, fieldLabel, action) {
  jQuery.ajax({
    url: WCBE_DATA.ajax_url,
    type: "post",
    dataType: "html",
    data: {
      action: "wcbe_column_manager_add_field",
      nonce: WCBE_DATA.ajax_nonce,
      field_name: fieldName,
      field_label: fieldLabel,
      field_action: action,
    },
    success: function (response) {
      jQuery(".wcbe-box-loading").hide();
      jQuery(".wcbe-column-manager-added-fields[data-action=" + action + "] .items").append(response);
      fieldName.forEach(function (name) {
        jQuery(".wcbe-column-manager-available-fields[data-action=" + action + "] input:checkbox[data-name=" + name + "]")
          .prop("checked", false)
          .closest("li")
          .attr("data-added", "true")
          .hide();
      });
      wcbeReInitColorPicker();
      jQuery(".wcbe-column-manager-check-all-fields-btn[data-action=" + action + "] input:checkbox").prop("checked", false);
      jQuery(".wcbe-column-manager-check-all-fields-btn[data-action=" + action + "] span")
        .removeClass("selected")
        .text("Select All");
      setTimeout(function () {
        wcbeSetColorPickerTitle();
      }, 250);
    },
    error: function () {},
  });
}

function wcbeAddMetaKeysManual(meta_key_name) {
  jQuery("#wcbe-add-meta-field-manual").attr("disabled", true);

  wcbeLoadingStart();
  jQuery.ajax({
    url: WCBE_DATA.ajax_url,
    type: "post",
    dataType: "html",
    data: {
      action: "wcbe_add_meta_keys_manual",
      nonce: WCBE_DATA.ajax_nonce,
      meta_key_name: meta_key_name,
    },
    success: function (response) {
      if (jQuery(".wcbe-meta-fields-items").length) {
        jQuery(".wcbe-meta-fields-items").append(response);
      } else {
        jQuery("#wcbe-meta-fields-items").append(response);
      }
      wcbeLoadingSuccess();
    },
    error: function () {
      wcbeLoadingError();
    },
  });
}

function wcbeAddACFMetaField(field_name, field_label, field_type) {
  wcbeLoadingStart();
  jQuery.ajax({
    url: WCBE_DATA.ajax_url,
    type: "post",
    dataType: "html",
    data: {
      action: "wcbe_add_acf_meta_field",
      nonce: WCBE_DATA.ajax_nonce,
      field_name: field_name,
      field_label: field_label,
      field_type: field_type,
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

function wcbeCheckFilterFormChanges() {
  let isChanged = false;
  jQuery('#wcbe-filter-form-content [data-field="value"]').each(function () {
    if (jQuery.isArray(jQuery(this).val())) {
      if (jQuery(this).val().length > 0) {
        isChanged = true;
      }
    } else {
      if (jQuery(this).val()) {
        isChanged = true;
      }
    }
  });
  jQuery('#wcbe-filter-form-content [data-field="from"]').each(function () {
    if (jQuery(this).val()) {
      isChanged = true;
    }
  });
  jQuery('#wcbe-filter-form-content [data-field="to"]').each(function () {
    if (jQuery(this).val()) {
      isChanged = true;
    }
  });

  jQuery("#filter-form-changed").val(isChanged);

  if (isChanged === true) {
    jQuery("#wcbe-bulk-edit-reset-filter").show();
  } else {
    jQuery('.wcbe-top-nav-status-filter a[data-status="all"]').addClass("active");
  }
}

function wcbeGetCheckedItem() {
  let itemIds;
  let itemsChecked = jQuery("input.wcbe-check-item:checkbox:checked");
  if (itemsChecked.length > 0) {
    itemIds = itemsChecked
      .map(function (i) {
        return jQuery(this).val();
      })
      .get();
  }

  return itemIds;
}

function wcbeGetTableCount(countPerPage, currentPage, total) {
  currentPage = currentPage ? currentPage : 1;
  let showingTo = parseInt(currentPage * countPerPage);
  let showingFrom = total > 0 ? parseInt(showingTo - countPerPage) + 1 : 0;
  showingTo = showingTo < total ? showingTo : total;
  return "Showing " + showingFrom + " to " + showingTo + " of " + total + " entries";
}

function wcbeSelectAllChecked() {
  return jQuery('.wcbe-check-item-main[value="all"]').prop("checked") === true;
}

function wcbeSelectVisibleChecked() {
  return jQuery('.wcbe-check-item-main[value="visible"]').prop("checked") === true;
}
