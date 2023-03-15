
/**
 * 获取k线
 * @param {*} code 
 * @param {*} ma 
 */
var fetchKlines = function(code, ma, limit = 286, timestamp = 0) {
    return $.get('/api/stock/klines', {code: code, ma:ma, limit:limit, timestamp: timestamp})
}

/**
 * 获取k线图标记
 * @param code 
 */
var fetchMarks = function(code) {
    return $.get('/api/stock/marks', {code: code})
}

/**
 * 添加K线标记
 * @param code 
 */
var fetchAddMarks = function(code) {
    return $.get('/api/stock/addMark', {code: code})
}