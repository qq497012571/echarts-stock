
/**
 * 发送localStorage消息
 * @param type 消息KEY
 * @param paylaod 消息body
 */
function sendMsg(type, paylaod) {
    localStorage.setItem('@@' + type, JSON.stringify({
        paylaod,
        temp: Date.now()
    }))
}

/**
 * 监听消息
 */
function listenMsg(handler) {
    const storageHandler = e => {
        const data = JSON.parse(e.newValue);
        handler(e.key.substring(2), data.paylaod)
    }
    window.addEventListener('storage', storageHandler)
    console.log('listen locationStorage event ...')
    return () => {
        window.removeEventListener('storage', storageHandler)
    }
}
