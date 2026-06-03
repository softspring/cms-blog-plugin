import {registerFeature} from '@softspring/cms-bundle/scripts/tools.js';
import {filterCurrentFilterElements} from '@softspring/cms-bundle/scripts/admin/content-edit/filter-preview.js';

let initialized = false;
const debug = true;

registerFeature('admin_content_edit_preview_article_list', initOnce);

if (document.readyState === 'complete') {
    initOnce();
}

function initOnce() {
    if (initialized) {
        logDebug('init skipped, already initialized');
        return;
    }

    initialized = true;
    logDebug('init listeners');
    document.addEventListener('change', onArticleListPreviewFieldChange);
    document.addEventListener('input', onArticleListPreviewFieldChange);
}

function onArticleListPreviewFieldChange(event) {
    const matchingField = getPreviewFieldFromTarget(event.target);
    logDebug('event received', {
        type: event.type,
        target: event.target,
        targetName: event.target ? event.target.name : null,
        targetValue: event.target ? event.target.value : null,
        matchingField: matchingField,
        fallbackParam: event.target ? getPreviewParamFromControlName(event.target.name) : null,
    });

    if (!matchingField) {
        logDebug('event ignored, target is not an article list preview field');
        return;
    }

    const moduleEdit = event.target.closest('.cms-module-edit');
    if (!moduleEdit) {
        logDebug('event ignored, .cms-module-edit not found', event.target);
        return;
    }

    logDebug('module edit found', moduleEdit);
    updateArticleListPreviews(moduleEdit);
}

function updateArticleListPreviews(moduleEdit) {
    const previews = moduleEdit.querySelectorAll('[data-article-list-preview]');
    logDebug('updating previews', {
        previewsCount: previews.length,
        paramFields: getPreviewFields(moduleEdit).map(function (field) {
            return {
                tagName: field.tagName,
                name: field.name,
                value: field.value,
                param: getPreviewParamName(field),
                inputLang: field.dataset.inputLang,
            };
        }),
    });

    previews.forEach(function (preview) {
        const params = getPreviewParams(moduleEdit, preview);
        const url = new URL(preview.dataset.previewUrl, window.location.origin);
        const previousUrl = preview.dataset.previewUrl;

        Object.keys(params).forEach(function (param) {
            if (params[param] === '') {
                url.searchParams.delete(param);
            } else {
                url.searchParams.set(param, params[param]);
            }
        });

        preview.dataset.previewUrl = url.pathname + url.search;
        delete preview.dataset.previewUrlLoaded;
        delete preview.dataset.previewUrlLoading;
        delete preview.dataset.previewUrlError;

        logDebug('preview url updated', {
            preview: preview,
            lang: preview.dataset.lang,
            site: preview.dataset.site,
            previousUrl: previousUrl,
            nextUrl: preview.dataset.previewUrl,
            params: params,
        });
    });

    logDebug('calling filterCurrentFilterElements');
    filterCurrentFilterElements();
}

function getPreviewParams(moduleEdit, preview) {
    const params = {};

    getPreviewParamNames(moduleEdit).forEach(function (param) {
        params[param] = getPreviewParamValue(moduleEdit, param, preview);
    });

    return params;
}

function getPreviewParamNames(moduleEdit) {
    return [...new Set(getPreviewFields(moduleEdit).map(function (field) {
        return getPreviewParamName(field);
    }))];
}

function getPreviewParamValue(moduleEdit, param, preview) {
    const fields = getPreviewFields(moduleEdit).filter(function (field) {
        return getPreviewParamName(field) === param;
    });
    const controls = fields.flatMap(function (field) {
        return isFormControl(field) ? [field] : [...field.querySelectorAll('input, select, textarea')];
    });

    const localizedControl = controls.find(function (control) {
        return getControlLanguage(control, param) === preview.dataset.lang;
    });

    const nonLocalizedControl = controls.find(function (control) {
        return !getControlLanguage(control, param);
    });

    const fallbackControl = controls[0];
    const selectedControl = localizedControl || nonLocalizedControl || fallbackControl;

    logDebug('param value resolved', {
        param: param,
        previewLang: preview.dataset.lang,
        fieldsCount: fields.length,
        controls: controls.map(function (control) {
            return {
                tagName: control.tagName,
                name: control.name,
                value: getFormControlValue(control),
                type: control.type,
                inputLang: control.dataset.inputLang,
                controlLanguage: getControlLanguage(control, param),
            };
        }),
        selectedControl: selectedControl,
        selectedValue: selectedControl ? getFormControlValue(selectedControl) : '',
    });

    return selectedControl ? getFormControlValue(selectedControl) : '';
}

function getPreviewFields(moduleEdit) {
    const fields = [...moduleEdit.querySelectorAll('[data-article-list-preview-param]')];

    moduleEdit.querySelectorAll('input, select, textarea').forEach(function (control) {
        if (fields.includes(control)) {
            return;
        }

        if (getPreviewParamFromControlName(control.name)) {
            fields.push(control);
        }
    });

    return fields;
}

function getPreviewFieldFromTarget(target) {
    if (!target) {
        return null;
    }

    return target.closest('[data-article-list-preview-param]') || (getPreviewParamFromControlName(target.name) ? target : null);
}

function getPreviewParamName(field) {
    return field.dataset.articleListPreviewParam || getPreviewParamFromControlName(field.name);
}

function getPreviewParamFromControlName(name) {
    if (!name) {
        return null;
    }

    if (name.match(/\[title\](?:\[[A-Za-z0-9_-]+])?$/)) {
        return 'title';
    }

    return null;
}

function isFormControl(field) {
    return ['INPUT', 'SELECT', 'TEXTAREA'].includes(field.tagName);
}

function getFormControlValue(field) {
    return 'checkbox' === field.type ? (field.checked ? '1' : '0') : field.value;
}

function getControlLanguage(control, param) {
    if (control.dataset.inputLang) {
        return control.dataset.inputLang;
    }

    const nameParts = [...control.name.matchAll(/\[([A-Za-z0-9_-]+)\]/g)].map(function (match) {
        return match[1];
    });

    if (param && nameParts.length >= 2 && nameParts[nameParts.length - 2] === param) {
        return nameParts[nameParts.length - 1];
    }

    const langContainer = control.closest('[data-lang]');

    return langContainer ? langContainer.dataset.lang : null;
}

function logDebug(message, data) {
    if (!debug) {
        return;
    }

    if (data === undefined) {
        console.debug('[article-list-preview]', message);
        return;
    }

    console.debug('[article-list-preview]', message, data);
}
