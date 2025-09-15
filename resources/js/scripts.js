function myLoad() {
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: false,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };
    addValidators();
}

function addValidators() {
    // Validation messages override
    $.validator.messages.required = function(param, input) {
        let $input = $(input);
        let title = $input.attr("title") || input.name.replace(/_/g, " ");
        return title + " is required";
    };

    // Custom rule for Indian mobile
    $.validator.addMethod("indianMobile", function(value, element) {
        return this.optional(element) || /^[6-9]\d{9}$/.test(value);
    }, "Enter a valid mobile number");
    
    $.validator.addMethod("strongPassword", function(value, element) {
        return this.optional(element) || 
            /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/.test(value);
    }, "Password must be at least 6 characters long and include 1 letter, 1 number, and 1 special character.");    

    $.validator.addMethod("vehicleNoFormat", function(value, element) {
        if (!value) return true; 
        let vehicles = value.split(",").map(v => v.trim()).filter(v => v.length > 0);
        let pattern = /^[A-Z]{2}\d{1,2}[A-Z]{1,2}\d{4}$/;
        return vehicles.every(v => pattern.test(v));
    }, "Please enter valid vehicle number(s)");

    $.validator.addMethod("vehicleCountMatch", function(value, element) {
        let requiredCount = parseInt($("#no_of_vehicles").val(), 10);
        if (!requiredCount || !value) return true; 
        let vehicles = value.split(",").map(v => v.trim()).filter(v => v.length > 0);
        return vehicles.length === requiredCount;
    }, "Number of vehicles entered must match the selected count");

    $.validator.addMethod("vehicleNoUnique", function(value, element) {
        if (!value) return true;
        let vehicles = value.split(",").map(v => v.trim().toUpperCase()).filter(v => v.length > 0);
        let unique = new Set(vehicles);
        return unique.size === vehicles.length;
    }, "Duplicate vehicle numbers are not allowed");

    $.validator.addMethod("timeRange", function(value, element) {
		if (!value) return true;
        let parts = value.split(":").slice(0,2);
        if (parts.length !== 2) return false;
        let hours = parseInt(parts[0], 10);
        let minutes = parseInt(parts[1], 10);
        if (isNaN(hours) || isNaN(minutes)) return false;
        let totalMinutes = hours * 60 + minutes;
        return totalMinutes >= 16 * 60 && totalMinutes <= (24 * 60);
    }, "Please select a time between 16:00 and 23:59");

    $.validator.methods.mydate = function (value, element) {
        return (
            this.optional(element) ||
            moment(value, appsettings.dtfmt, true).isValid()
        );
    };
    $.validator.methods.mydatetime = function (value, element) {
        return (
            this.optional(element) ||
            moment(value, appsettings.dttmfmt, true).isValid()
        );
    };
    $.validator.methods.greaterThanDate = function (value, element, param) {
        if (this.optional(element)) return true;
        const format = appsettings.dtfmt || "DD-MM-YYYY";
        const startVal = $(param).val();
        if (!value || !startVal) return true;
        const endDate = moment(value, format, true);
        const startDate = moment(startVal, format, true);
        return (
            endDate.isValid() &&
            startDate.isValid() &&
            endDate.isAfter(startDate)
        );
    };
    $.validator.methods.greaterThanDateTime = function (value, element, param) {
        if (this.optional(element)) return true;
        const format = appsettings.dttmfmt || "DD-MM-YYYY HH:mm";
        const startVal = $(param).val();
        if (!value || !startVal) return true;
        const endDate = moment(value, format, true);
        const startDate = moment(startVal, format, true);
        return (
            endDate.isValid() &&
            startDate.isValid() &&
            endDate.isAfter(startDate)
        );
    };
}

function loading(show) {
    if (show) $("#loader").removeClass("d-none");
    else $("#loader").addClass("d-none");
}

function undef(vl) {
    return typeof vl === "undefined";
}

function getval(nm, def) {
    def = undef(def) ? "" : def;
    return !undef(nm) ? nm : def;
}

function fetchOpt(opts, nm, def) {
    def = undef(def) ? "" : def;
    try {
        return getval(opts[nm], def);
    } catch (err) {
        return def;
    }
}

function objS(msg, trim = 0) {
    if (Array.isArray(msg) || typeof msg === "object") {
        msg = JSON.stringify(msg);
    }
    return trim == 0 ? msg : msg.slice(0, trim).trim() + "...";
}

function myAlert(msg, typ="primary", yesText="", yesAct=null, noText="", noAct=null, noclose=false) {
    //console.log("SUDIPTA >> ", msg, typ, yesText, yesAct, noText, noAct);
    msg = objS(msg);

    // Toast body
    $("#myToast").find(".toast-body").html(msg);

    // Border color class update
    const $toastInner = $("#myToast .d-flex");
    $toastInner
        .removeClass(function (index, className) {
            return (className.match(/border-\S+/g) || []).join(" ");
        })
        .addClass("border-start border-4 border-" + typ);

    // Icon update
    const $icon = $("#toastIcon");
    const iconMap = {
        success: "bi-check-circle-fill",
        danger: "bi-x-circle-fill",
        warning: "bi-exclamation-triangle-fill",
        info: "bi-info-circle-fill",
        primary: "bi-megaphone",
        secondary: "bi-dot-circle-fill",
    };
    $icon.removeClass().addClass("bi me-3 big-toast-icon text-" + typ);
    $icon.addClass(iconMap[typ] || "bi-bell-fill");

    // Buttons
    const $btnContainer = $("#toastButtons");
    $btnContainer.empty();
    let callbackToRunAfterClose = null;

    if (yesText) {
        const $yesBtn = $(
            '<button type="button" class="exbtn btn btn-' +
                typ +
                ' btn-sm me-2"></button>'
        ).text(yesText);
        $yesBtn.on("click", function () {
            if (yesAct) callbackToRunAfterClose = yesAct;
            bootstrap.Toast.getInstance($("#myToast")[0]).hide();
        });
        $btnContainer.append($yesBtn);
    }
    if (noText) {
        const $noBtn = $(
            '<button type="button" class="exbtn btn btn-' +
                typ +
                ' btn-sm"></button>'
        ).text(noText);
        $noBtn.on("click", function () {
            if (noAct) callbackToRunAfterClose = noAct;
            bootstrap.Toast.getInstance($("#myToast")[0]).hide();
        });
        $btnContainer.append($noBtn);
    }

    // Backdrop + callbacks
    $("#toastBackdrop").removeClass("d-none");
    $("#myToast")
        .off("hidden.bs.toast")
        .on("hidden.bs.toast", function () {
            $("#toastBackdrop").addClass("d-none");
            if (typeof callbackToRunAfterClose === "function")
                setTimeout(callbackToRunAfterClose, 10);
        });

    let closeBtn = $("#myToast").find(".btn-close");
    if(noclose) closeBtn.hide();
    else closeBtn.show();
    
    // Show
    const toast = new bootstrap.Toast($("#myToast"));
    toast.show();
}

function xFocus(elm) {
    $(typeof elm === "string" ? "#" + elm : elm)
        .focus()[0]
        .scrollIntoView({ behavior: "smooth", block: "center" });
}

function webserv(typ, api, dt, f1 = null, f2 = null) {
    let isFormData = dt instanceof FormData;

    if (!isFormData && typeof dt === "string") {
        // MEANS FORM NAME
        const formData = {};
        $("#" + dt)
            .serializeArray()
            .forEach((field) => {
                if (formData[field.name]) {
                    if (Array.isArray(formData[field.name])) {
                        formData[field.name].push(field.value);
                    } else {
                        formData[field.name] = [
                            formData[field.name],
                            field.value,
                        ];
                    }
                } else {
                    formData[field.name] = field.value;
                }
            });
        dt = formData;
    }

    if (!isFormData && typ != "GET") {
        dt._token = $('meta[name="csrf-token"]').attr("content");
    }
    if (!isFormData && typ === "PUT") {
        dt._method = "PUT";
        typ = "POST";
    }

    const params = {
        type: typ,
        dataType: "json",
        url: api,
        success: function (data) {
            loading(false);
            if (data) {
                if (data["success"]) {
                    if (f1) f1(data);
                } else {
                    if (f2) f2(data);
                    else toastr.error(data["msg"]);
                }
            } else {
                if (f2) f2(data);
                else toastr.error(objS(data));
            }
        },
        error: function (req, stat, err) {
            loading(false);
            if (f2) f2("service fail");
            else toastr.error(objS([req, stat, err], 200));
        },
    };

    if (dt) {
        if (isFormData) {
            params.data = dt;
            params.processData = false;
            params.contentType = false;
        } else if (Object.keys(dt).length > 0) {
            if (typ === "GET") {
                // Send as query params
                params.data = dt;
            } else {
                // Send JSON body
                params.data = JSON.stringify(dt);
                params.contentType = "application/json";
            }
        }
    }
    $.ajax(params);
}

function goLnk(lnk) {
    window.open(lnk, "_self", false);
}
