jQuery(document).ready(function ($) {
    "use strict";

    $(document).on("click", "#wcbe-bulk-edit-form-schedule-bulk", function () {
        $('.wcbe-tab-item[data-content="set_schedule"]').show().trigger("click");
    });

    $(document).on("click", "#wcbe-bulk-edit-form-reset", function () {
        $(".wcbe-set-schedule-enable-schedule").prop("checked", false).change();
        $(".wcbe-set-schedule-run-at").val("now").change();
    });

    $(document).on("change", ".wcbe-set-schedule-enable-schedule", function () {
        let container = $(this).closest("#wcbe-float-side-modal-bulk-edit");
        if (!container.length) {
            return;
        }

        if ($(this).prop("checked") === true) {
            container.find(".wcbe-set-schedule-form").show();
            container.find(".wcbe-set-schedule-run-at").change();
            $(".wcbe-bulk-edit-form-schedule-bulk-edit").show();
            $("#wcbe-bulk-edit-form-do-bulk-edit").hide();
        } else {
            container.find(".wcbe-set-schedule-form").hide();
            $(".wcbe-bulk-edit-form-schedule-bulk-edit").hide();
            $("#wcbe-bulk-edit-form-do-bulk-edit").show();
        }
    });

    $(document).on("change", ".wcbe-set-schedule-run-at", function () {
        let container;
        if ($(this).closest("#wcbe-modal-schedule-edit-job").length) {
            container = $(this).closest("#wcbe-modal-schedule-edit-job");
        } else {
            container = $('.wcbe-tab-content-item[data-content="set_schedule"]');
        }

        container.find(".wcbe-set-schedule-dependent > div").hide();

        let dependentElement = container.find(".wcbe-set-schedule-dependent");
        if (!dependentElement.length) {
            return;
        }

        if ($(this).val() != "") {
            dependentElement.find('div[data-content="revert_changes"]').show();
        } else {
            dependentElement.find('div[data-content="revert_changes"]').hide();
        }

        if ($(this).val() == "later") {
            dependentElement.find('div[data-content="later"]').show();
            dependentElement.find(".wcbe-set-schedule-run-for").change();
        } else {
            dependentElement.find('div[data-content="later"]').hide();
        }

        if ($(this).val() == "now") {
            dependentElement.find('div[data-content="stop_schedule"]').hide();
            dependentElement.find('div[data-content="now"]').show();
        } else {
            dependentElement.find('div[data-content="now"]').hide();
        }
    });

    $(document).on("change", ".wcbe-set-schedule-run-for", function () {
        let container;
        if ($(this).closest("#wcbe-modal-schedule-edit-job").length) {
            container = $(this).closest("#wcbe-modal-schedule-edit-job");
        } else {
            container = $('.wcbe-tab-content-item[data-content="set_schedule"]');
        }

        let dependentElement = container.find(".wcbe-set-schedule-dependent");
        if (!dependentElement.length) {
            return;
        }

        if ($(this).val() == "once") {
            dependentElement.find('div[data-content="once"]').show();
            dependentElement.find('div[data-content="stop_schedule"]').hide();
            dependentElement.find(".wcbe-set-schedule-once-type").change();
        } else {
            if ($(this).val() != "") {
                dependentElement.find('div[data-content="stop_schedule"]').show();
            } else {
                dependentElement.find('div[data-content="stop_schedule"]').hide();
            }
            dependentElement.find('div[data-content="once"]').hide();
            dependentElement.find('div[data-content="specific_date_time"]').hide();
            dependentElement.find('div[data-content="n_hours_later"]').hide();
            dependentElement.find('div[data-content="n_days_later"]').hide();
        }

        if ($(this).val() == "daily") {
            dependentElement.find('div[data-content="daily"]').show();
        } else {
            dependentElement.find('div[data-content="daily"]').hide();
        }

        if ($(this).val() == "weekly") {
            dependentElement.find('div[data-content="weekly"]').show();
        } else {
            dependentElement.find('div[data-content="weekly"]').hide();
        }

        if ($(this).val() == "monthly") {
            dependentElement.find('div[data-content="monthly"]').show();
        } else {
            dependentElement.find('div[data-content="monthly"]').hide();
        }
    });

    $(document).on("change", ".wcbe-set-schedule-once-type", function (e) {
        let container;
        if ($(this).closest("#wcbe-modal-schedule-edit-job").length) {
            container = $(this).closest("#wcbe-modal-schedule-edit-job");
        } else {
            container = $('.wcbe-tab-content-item[data-content="set_schedule"]');
        }

        let dependentElement = container.find(".wcbe-set-schedule-dependent");
        if (!dependentElement.length) {
            return;
        }

        dependentElement.find('div[data-content="specific_date_time"]').hide();
        dependentElement.find('div[data-content="n_hours_later"]').hide();
        dependentElement.find('div[data-content="n_days_later"]').hide();

        if ($(this).val() != "") {
            dependentElement.find('div[data-content="' + $(this).val() + '"]').show();
        }
    });

    $(document).on("click", ".wcbe-schedule-bulk-edit-button", function (e) {
        $(".wcbe-schedule-multiple-bulk-edit-container").slideToggle(100);
    });

    $(document).on("click", '.wcbe-schedule-jobs-list-action-button[data-action="delete"]', function (e) {
        let jobId = $(this).attr("data-id");
        swal(
            {
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeScheduleDeleteJob(jobId);
                }
            }
        );
    });

    $(document).on("click", '.wcbe-schedule-jobs-list-action-button[data-action="stop"]', function (e) {
        let jobId = $(this).attr("data-id");
        swal(
            {
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wcbe-button wcbe-button-lg wcbe-button-white",
                confirmButtonClass: "wcbe-button wcbe-button-lg wcbe-button-green",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    wcbeScheduleStopJob(jobId);
                }
            }
        );
    });

    $(document).on("click", ".wcbe-schedule-edit-job-apply-button", function (e) {
        wcbeScheduleUpdateJob($(this).attr("data-id"));
    });

    $(document).on("click", '.wcbe-schedule-jobs-list-action-button[data-action="log"]', function (e) {
        $(".wcbe-schedule-job-log-loading").show();
        $(".wcbe-schedule-job-log-container").html("");
        wcbeScheduleGetJobLog($(this).attr("data-id"));
    });

    $(document).on("click", '.wcbe-schedule-jobs-list-action-button[data-action="show_edit_items"]', function (e) {
        $(".wcbe-schedule-job-edit-items-loading").show();
        $(".wcbe-schedule-job-edit-items-container").html("");
        wcbeScheduleGetEditItems($(this).attr("data-id"));
    });

    $(document).on("click", '.wcbe-schedule-jobs-list-action-button[data-action="edit"]', function (e) {
        $(".wcbe-schedule-edit-job-container").hide();
        $(".wcbe-schedule-edit-job-loading").show();
        wcbeScheduleGetJobData($(this).attr("data-id"));
    });

    $(document).on("click", 'a[data-target="#wcbe-float-side-modal-schedule-jobs"]', function () {
        $.ajax({
            url: WCBE_DATA.ajax_url,
            type: "post",
            dataType: "json",
            data: {
                action: WCBE_SCHEDULE_DATA.identifier + "_get_schedule_jobs",
                nonce: WCBE_DATA.ajax_nonce,
            },
            success: function (response) {
                $("#wcbe-float-side-modal-schedule-jobs table tbody")
                    .html(response.rows)
                    .ready(function () {
                        wcbeSetTipsyTooltip();
                    });
                wcbeScheduleAwaitingCountUpdate(response.awaiting_count);
            },
            error: function () { },
        });
    });

    $(document).on("click", '.wcbe-tab-item[data-content="set_schedule"]', function () {
        wcbeScheduleCurrentTimeUpdate();
    });

    $(document).on("click", ".wcbe-schedule-current-time-update-button", function () {
        jQuery(".wcbe-set-schedule-current-time").css({ color: "#9d9d9d" });
        wcbeScheduleCurrentTimeUpdate();
    });

    wcbeScheduleDateTimePickerInit();
});

function wcbeScheduleCurrentTimeUpdate() {
    jQuery.ajax({
        url: WCBE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: "wcbe_schedule_get_current_time",
            nonce: WCBE_DATA.ajax_nonce,
        },
        success: function (response) {
            jQuery(".wcbe-set-schedule-current-time").html(response.time).css({ color: "#444" });
        },
        error: function () { },
    });
}

function wcbeScheduleStopJob(jobId) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_SCHEDULE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: WCBE_SCHEDULE_DATA.identifier + "_schedule_job_stop",
            nonce: WCBE_SCHEDULE_DATA.ajax_nonce,
            job_id: jobId,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-float-side-modal-schedule-jobs table tbody")
                    .html(response.rows)
                    .ready(function () {
                        wcbeLoadingSuccess();
                        wcbeScheduleAwaitingCountUpdate(response.awaiting_count);
                    });
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeScheduleUpdateJob(jobId) {
    wcbeLoadingStart();

    let container = jQuery("#wcbe-modal-schedule-edit-job .wcbe-set-schedule-form");
    container
        .find(".required:visible")
        .each(function () {
            if (jQuery(this).val() != "") {
                jQuery(this).removeClass("error");
            } else {
                jQuery(this).addClass("error");
            }
        })
        .promise()
        .done(function () {
            if (container.find(".error").length) {
                wcbeLoadingError("Please fill the required fields");
                return;
            }

            let dates = wcbeScheduleGetDatesFromJobForm(container);
            jQuery.ajax({
                url: WCBE_SCHEDULE_DATA.ajax_url,
                type: "post",
                dataType: "json",
                data: {
                    action: WCBE_SCHEDULE_DATA.identifier + "_schedule_update_job",
                    nonce: WCBE_SCHEDULE_DATA.ajax_nonce,
                    job_id: jobId,
                    label: container.find(".wcbe-set-schedule-name").val(),
                    description: container.find(".wcbe-set-schedule-description").val(),
                    run_at: container.find(".wcbe-set-schedule-run-at").val(),
                    run_for: container.find(".wcbe-set-schedule-run-for:visible").length ? container.find(".wcbe-set-schedule-run-for").val() : null,
                    dates: dates,
                    stop_date: container.find(".wcbe-set-schedule-stop-date-time:visible").length ? container.find(".wcbe-set-schedule-stop-date-time").val() : null,
                    revert_date: container.find(".wcbe-set-schedule-revert-date-time:visible").length ? container.find(".wcbe-set-schedule-revert-date-time").val() : null,
                },
                success: function (response) {
                    if (response.success) {
                        if (container.find(".wcbe-set-schedule-run-at").val() == "now") {
                            wcbeLoadingSuccess("Success | Reloading ...");
                            location.reload();
                        } else {
                            wcbeLoadingSuccess();
                            jQuery(".wcbe-schedule-jobs-list-navigation-button>a").trigger("click");
                            wcbeCloseModal();
                        }
                    } else {
                        wcbeLoadingError();
                    }
                },
                error: function () {
                    wcbeLoadingError();
                },
            });
        });
}

function wcbeScheduleDeleteJob(jobId) {
    wcbeLoadingStart();
    jQuery.ajax({
        url: WCBE_SCHEDULE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: WCBE_SCHEDULE_DATA.identifier + "_schedule_job_delete",
            nonce: WCBE_SCHEDULE_DATA.ajax_nonce,
            job_id: jobId,
        },
        success: function (response) {
            if (response.success) {
                jQuery("#wcbe-float-side-modal-schedule-jobs table tbody")
                    .html(response.rows)
                    .ready(function () {
                        wcbeLoadingSuccess();
                        wcbeScheduleAwaitingCountUpdate(response.awaiting_count);
                    });
            } else {
                wcbeLoadingError();
            }
        },
        error: function () {
            wcbeLoadingError();
        },
    });
}

function wcbeScheduleGetJobLog(jobId) {
    jQuery.ajax({
        url: WCBE_SCHEDULE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: WCBE_SCHEDULE_DATA.identifier + "_schedule_get_job_log",
            nonce: WCBE_SCHEDULE_DATA.ajax_nonce,
            job_id: jobId,
        },
        success: function (response) {
            jQuery(".wcbe-schedule-job-log-loading").hide();
            if (response.success) {
                jQuery(".wcbe-schedule-job-log-container")
                    .html(response.html)
                    .ready(function () {
                        wcbeFixModalHeight(jQuery("#wcbe-modal-schedule-job-log"));
                        wcbeSetTipsyTooltip();
                    });
            } else {
                jQuery(".wcbe-schedule-job-log-container").html("No data available");
            }
        },
        error: function () {
            jQuery(".wcbe-schedule-job-log-container").html("No data available");
        },
    });
}

function wcbeScheduleGetJobData(jobId) {
    jQuery.ajax({
        url: WCBE_SCHEDULE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: WCBE_SCHEDULE_DATA.identifier + "_schedule_get_job_data",
            nonce: WCBE_SCHEDULE_DATA.ajax_nonce,
            job_id: jobId,
        },
        success: function (response) {
            jQuery(".wcbe-schedule-edit-job-loading").hide();
            if (response.success && response.job.id) {
                let container = jQuery(".wcbe-schedule-edit-job-container");
                container.find(".wcbe-set-schedule-name").val(response.job.label).change();
                container.find(".wcbe-set-schedule-description").val(response.job.description).change();
                container.find(".wcbe-set-schedule-run-at").val(response.job.run_at).change();
                container.find(".wcbe-set-schedule-run-for").val(response.job.run_for).change();
                if (response.job.run_for != "") {
                    switch (response.job.run_for) {
                        case "once":
                            if (response.job.dates.type) {
                                container.find(".wcbe-set-schedule-once-type").val(response.job.dates.type).change();
                                switch (response.job.dates.type) {
                                    case "specific_date_time":
                                        container.find(".wcbe-set-schedule-once-date-time").val(response.job.dates.date_time).change();
                                        break;
                                    case "n_hours_later":
                                        container.find(".wcbe-set-schedule-once-hours").val(response.job.dates.time).change();
                                        break;
                                    case "n_days_later":
                                        container.find(".wcbe-set-schedule-once-days").val(response.job.dates.days).change();
                                        break;
                                }
                            }
                            break;
                        case "daily":
                            if (response.job.dates.time) {
                                container.find(".wcbe-set-schedule-daily-time").val(response.job.dates.time).change();
                            }
                            break;
                        case "weekly":
                            if (response.job.dates.days && response.job.dates.time) {
                                container.find(".wcbe-set-schedule-weekly-days").val(response.job.dates.days).change();
                                container.find(".wcbe-set-schedule-weekly-time").val(response.job.dates.time).change();
                            }
                            break;
                        case "monthly":
                            if (response.job.dates.days && response.job.dates.time) {
                                container.find(".wcbe-set-schedule-monthly-days").val(response.job.dates.days).change();
                                container.find(".wcbe-set-schedule-monthly-time").val(response.job.dates.time).change();
                            }
                            break;
                    }
                }
                container.find(".wcbe-set-schedule-stop-date-time").val(response.job.stop_date).change();
                container.find(".wcbe-set-schedule-revert-date-time").val(response.job.revert_date).change();
                container.show();

                setTimeout(function () {
                    wcbeFixModalHeight(jQuery("#wcbe-modal-schedule-edit-job"));
                    jQuery(".wcbe-schedule-edit-job-apply-button").attr("data-id", response.job.id).prop("disabled", false);
                }, 150);
            } else {
            }
        },
        error: function () { },
    });
}

function wcbeScheduleGetEditItems(jobId) {
    jQuery.ajax({
        url: WCBE_SCHEDULE_DATA.ajax_url,
        type: "post",
        dataType: "json",
        data: {
            action: WCBE_SCHEDULE_DATA.identifier + "_schedule_get_edit_items",
            nonce: WCBE_SCHEDULE_DATA.ajax_nonce,
            job_id: jobId,
        },
        success: function (response) {
            jQuery(".wcbe-schedule-job-edit-items-loading").hide();
            if (response.success && response.edit_items) {
                jQuery(".wcbe-schedule-job-edit-items-container").html(response.edit_items);

                setTimeout(function () {
                    wcbeFixModalHeight(jQuery("#wcbe-modal-schedule-job-edit-items"));
                }, 150);
            }
        },
        error: function () { },
    });
}

var wcbeScheduleAllowTimes = [];
for (let hour = 0; hour < 24; hour++) {
    for (let minute = 0; minute < 60; minute += 5) {
        let formattedHour = ("0" + hour).slice(-2);
        let formattedMinute = ("0" + minute).slice(-2);
        wcbeScheduleAllowTimes.push(formattedHour + ":" + formattedMinute);
    }
}

function wcbeScheduleDateTimePickerInit() {
    if (!jQuery.fn.datetimepicker) {
        return false;
    }

    jQuery(".wcbe-schedule-datetimepicker").datetimepicker("destroy");
    jQuery(".wcbe-schedule-timepicker").datetimepicker("destroy");
    jQuery(".wcbe-schedule-datetimepicker").datetimepicker({
        format: "Y-m-d H:i",
        scrollMonth: false,
        scrollInput: false,
        allowTimes: wcbeScheduleAllowTimes,
    });
    jQuery(".wcbe-schedule-timepicker").datetimepicker({
        datepicker: false,
        format: "H:i",
        scrollMonth: false,
        scrollInput: false,
        allowTimes: wcbeScheduleAllowTimes,
    });
}

function wcbeScheduleAwaitingCountUpdate(awaitingCount) {
    if (awaitingCount && parseInt(awaitingCount) > 0) {
        if (jQuery(".wcbe-jobs-list-button-number").length) {
            jQuery(".wcbe-jobs-list-button-number").text(awaitingCount);
        } else {
            jQuery(".wcbe-schedule-jobs-list-navigation-button").append('<span class="wcbe-jobs-list-button-number">' + awaitingCount + "</span>");
        }
    } else {
        jQuery(".wcbe-jobs-list-button-number").remove();
    }
}

function wcbeScheduleGetDatesFromJobForm(containerElement) {
    let dates = {};

    if (containerElement.find(".wcbe-set-schedule-run-for").val() != "") {
        switch (containerElement.find(".wcbe-set-schedule-run-for").val()) {
            case "once":
                dates["type"] = containerElement.find(".wcbe-set-schedule-once-type").val();
                switch (containerElement.find(".wcbe-set-schedule-once-type").val()) {
                    case "specific_date_time":
                        dates["date_time"] = containerElement.find(".wcbe-set-schedule-once-date-time").val();
                        break;
                    case "n_hours_later":
                        dates["time"] = containerElement.find(".wcbe-set-schedule-once-hours").val();
                        break;
                    case "n_days_later":
                        dates["days"] = containerElement.find(".wcbe-set-schedule-once-days").val();
                        break;
                }
                break;
            case "daily":
                dates["time"] = containerElement.find(".wcbe-set-schedule-daily-time").val();
                break;
            case "weekly":
                dates["days"] = containerElement.find(".wcbe-set-schedule-weekly-days").val();
                dates["time"] = containerElement.find(".wcbe-set-schedule-weekly-time").val();
                break;
            case "monthly":
                dates["days"] = containerElement.find(".wcbe-set-schedule-monthly-days").val();
                dates["time"] = containerElement.find(".wcbe-set-schedule-monthly-time").val();
                break;
        }
    }

    return dates;
}
