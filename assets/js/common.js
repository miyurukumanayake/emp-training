// noinspection JSUnusedGlobalSymbols,TypeScriptUMDGlobal,JSJQueryEfficiency

/**
 * User object
 * @type {{ email: string, name: string, role: string, id: number } | null}
 */
let user = null;

/**
 * Make an HTTP request
 * @param {"GET" | "POST"} method HTTP method
 * @param {string} api API endpoint
 * @param {object} data Data to send in the request
 * @returns {Promise<{ status: 200 | 201, data?: object }>} Response from the server
 */
function http(method, api, data = {}) {
    return new Promise((resolve, reject) => {
        $.ajax(
            {
                url: `server/${api}.php`,
                type: method,
                data,
                dataType: "json",
                success: (data) => {
                    if (data.status === 200 || data.status === 201) {
                        resolve({status: data.status, data});
                    } else {
                        reject({status: data.status, error: typeof data === "string" ? {message: data} : data});
                    }
                },
                error: (err) => {
                    reject({status: 500, error: err});
                }
            }
        );
    });
}

/**
 * Make a GET request
 * @param {string} api API endpoint
 * @returns {Promise<{ status: 200 | 201, data?: object }>} Response from the server
 */
function httpGet(api) {
    return http("GET", api);
}

/**
 * Make a POST request
 * @param {string} api API endpoint
 * @param {object} data Data to send in the request
 * @returns {Promise<{ status: 200 | 201, data?: object }>} Response from the server
 */
function httpPost(api, data) {
    return http("POST", api, data);
}

/**
 * Remove all scripts from pages/ folder
 */
function removePageScripts() {
    const scripts = document.querySelectorAll(`script[src^="pages/"]`);
    scripts.forEach((script) => script.remove());
}

function removePageStyles() {
    const links = document.querySelectorAll('link[rel="stylesheet"][href^="pages/"]');
    links.forEach((link) => link.remove());
}


/**
 * Load a script from pages/ folder
 * @param {string} page
 */
function loadScript(page) {
    $.getScript(`pages/${page}/${page}.js?ts=${new Date().getTime()}`, function () {
    });
}

function loadCSS(page) {
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = `pages/${page}/${page}.css?ts=${new Date().getTime()}`;
    document.head.appendChild(link);
}

/**
 * Load a page into the content div
 * @param {string} page
 */
function loadPage(page) {
    removePageScripts();
    removePageStyles();
    $("#content").load(`pages/${page}/${page}.php`, function () {
        loadScript(page);
        loadCSS(page);
    });
}

/**
 * Check if the user is authenticated
 * @returns {Promise<boolean>} True if the user is authenticated, false otherwise
 */
async function checkAuth() {
    return new Promise((resolve) => {
        httpPost("authenticate", {}).then(() => {
            resolve(true);
        }).catch(() => {
            resolve(false);
        });
    });
}

/**
 * On Login
 */
onLoggedIn = () => {
    $('#header').show();
    $('#menu').show();
    $('#footer').show();
    $('#content').addClass('logged-in');
    loadPage("main");
}

/**
 * On Logout
 */
onLoggedOut = () => {
    $('#header').hide();
    $('#menu').hide();
    $('#footer').hide();
    $('#content').removeClass('logged-in');
    loadPage("login");
}

confirmModal = async (title, message, confirmButton, buttonClass) => {
    const confirmModalElm = $('#confirmModal');
    const confirmModal = new bootstrap.Modal(confirmModalElm, { backdrop: 'static', keyboard: false });

    return new Promise((resolve) => {
        confirmModalElm.find($('[data-modal-title]')).text(title ? title : "Confirm");
        confirmModalElm.find($('[data-modal-body]')).text(message);
        confirmModalElm.find($('[data-modal-confirm]')).text(confirmButton).addClass(buttonClass);

        confirmModal.show();

        confirmModalElm.find($('[data-modal-close]')).on('click', () => {
            confirmModal.hide();
            confirmModalElm.find($('[data-modal-close]')).off('click');
            resolve(false);
        });

        confirmModalElm.find($('[data-modal-confirm]')).on('click', () => {
            confirmModal.hide();
            confirmModalElm.find($('[data-modal-confirm]')).off('click');
            resolve(true);
        });
    });
}

$(() => {
    $("[data-download]").on('click', async function () {
        const file = $(this).data('file');
        const name = $(this).data('name');
        const ext = file.split('.').pop();

        /** @type {string} */
        let filename = file.split('/').pop();

        if (name) {
            filename = name;
            if (!name.endsWith('.' + ext)) {
                filename += '.' + ext;
            }
        }

        $.ajax({
            url: '/server/download-file.php',
            method: 'POST',
            data: { file, filename },
            xhrFields: { responseType: 'blob' },
            success: function (response) {
                const blob = new Blob([response]);
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(link.href);
            },
            error: function (xhr) {
                alert('Error downloading file: ' + xhr.responseText);
            }
        });
    });
});