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

function myAlert(msg, typ, yesText, yesAct, noText, noAct) {
    typ = getval(typ, "success");
    msg = objS(msg);

    $("#myToast").find(".toast-body").html(msg);
    const $btnContainer = $("#toastButtons");
    $btnContainer.empty();
    let callbackToRunAfterClose = null;
    if (!undef(yesText)) {
        const $yesBtn = $(
            '<button type="button" class="exbtn btn btn-' +
                typ +
                ' btn-sm me-2"></button>'
        ).text(yesText);
        $yesBtn.on("click", function () {
            if (typeof yesAct === "function") {
                callbackToRunAfterClose = yesAct;
            }
            bootstrap.Toast.getInstance($("#myToast")[0]).hide();
        });
        $btnContainer.append($yesBtn);
    }
    if (!undef(noText)) {
        const $noBtn = $(
            '<button type="button" class="exbtn btn btn-' +
                typ +
                ' btn-sm"></button>'
        ).text(noText);
        $noBtn.on("click", function () {
            if (typeof noAct === "function") {
                callbackToRunAfterClose = noAct;
            }
            bootstrap.Toast.getInstance($("#myToast")[0]).hide();
        });
        $btnContainer.append($noBtn);
    }
    $("#toastBackdrop").removeClass("d-none");
    $("#myToast").off("hidden.bs.toast");
    $("#myToast").on("hidden.bs.toast", function () {
        $("#toastBackdrop").addClass("d-none");
        if (typeof callbackToRunAfterClose === "function") {
            setTimeout(callbackToRunAfterClose, 10);
        }
    });
    const toast = new bootstrap.Toast($("#myToast"));
    toast.show();
}

function xFocus(elm) {
    $(typeof elm === "string" ? "#" + elm : elm)
        .focus()[0]
        .scrollIntoView({ behavior: "smooth", block: "center" });
}

function webserv(typ, api, dt, f1 = null, f2 = null) {
    let isFormData = (dt instanceof FormData);
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

    if (!isFormData && typ != "GET") dt._token = $('meta[name="csrf-token"]').attr("content");
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
            params.data = JSON.stringify(dt);
            params.contentType = "application/json";
        }
    }

    $.ajax(params);
}

function goLnk(lnk) {
    window.open(lnk, "_self", false);
}
