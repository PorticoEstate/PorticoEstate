
export function getSearchDateString(date) {
    return `${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()}`;
}

export function getSearchTimeString(date) {
    return `${("0" + date.getHours()).slice(-2)}:${("0" + date.getMinutes()).slice(-2)}`
}

export function getFullTimeString(date) {
    return `${("0" + date.getHours()).slice(-2)}:${("0" + date.getMinutes()).slice(-2)}:${("0" + date.getSeconds()).slice(-2)}`
}

export function getSearchDatetimeString(date) {
    return `${getSearchDateString(date)} ${getSearchTimeString(date)}`;
}

export function getIsoDateString(date) {
    return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
}

export function getDateFromSearch(dateString) {
    // Normalize the divider to a hyphen
    const normalizedDateStr = dateString.replace(/[.\/]/g, '-');

    // Split the date into its components
    const [day, month, year] = normalizedDateStr.split('-').map(num => parseInt(num, 10));

    // Create a DateTime object
    const dt = luxon.DateTime.local(year, month, day);

    return dt.toJSDate();
}

export function getAllSubRowsIds(rows, id) {
    let result = [id];
    rows.filter(a => a.parent_id === id).map(a => {
        result.push(...getAllSubRowsIds(rows, a.id));
    });
    return result;
}

export function emptySearch() {
    const el = $("#search-result");
    el.empty();
    return el;
}

export function fillSearchCount(data, count = 0) {
    const el = $("#search-count");
    el.empty();
    if (data) {
        el.append(`Antall treff: ${data.length}${count > 0 ? " av " + count : ""}`);
    }
}

export function htmlDecode(input) {
    const doc = new DOMParser().parseFromString(input, "text/html");
    const newInput = doc.documentElement.textContent;
    // Some texts are double encoded
    const newDoc = new DOMParser().parseFromString(newInput, "text/html");
    return newDoc.documentElement.textContent;
}

export function joinWithDot(texts) {
    return texts.map(t => t && t.length > 0 ? `<span class="d-flex align-items-center">${t}</span>` : null).filter(t => t).join(`<span class="slidedown__toggler__info__separator"><i class="fa-solid fa-circle"></i></span>`)
}

export function sortOnField(data, field) {
    return data.sort((a, b) => a[field]?.localeCompare(b[field], "no"))
}

export function sortOnName(data) {
    return sortOnField(data, 'name')
}

export function arraysAreEqual(arr1, arr2) {
    if (arr1.length !== arr2.length) {
        return false;
    }
    for (let i = 0; i < arr1.length; i++) {
        if (arr1[i] !== arr2[i]) {
            return false;
        }
    }
    return true;
}
