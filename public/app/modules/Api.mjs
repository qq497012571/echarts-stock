
/**
 * 获取k线
 * @param code 
 * @param ma 
 */
let fetchKlines = function(code, ma, limit = 286, timestamp = 0) {
    return $.get('/api/stock/klines', {code: code, ma:ma, limit:limit, timestamp: timestamp})
}

/**
 * 获取k线图标记
 * @param code 
 */
let fetchMarks = function(code) {
    return $.get('/api/stock/marks', {code: code})
}

/**
 * 添加K线标记
 * @param data 
 */
let fetchAddMarks = function(data) {
    return $.post('/api/stock/addMark', data)
}

/**
 * 移除K线标记
 * @param data 
 */
let fetchRemoveMarks = function(data) {
    return $.post('/api/stock/removeMark', data)
}

/**
 * 移除K线标记
 * @param data 
 */
let fetchCancelUserStock = function(data) {
    return $.post('/api/user_stock/cancel', data)
}

export {fetchKlines, fetchMarks, fetchAddMarks, fetchRemoveMarks, fetchCancelUserStock}