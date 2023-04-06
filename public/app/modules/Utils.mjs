
/**
 * 获取url查询参数
 * @param {string} name 
 * @returns 
 */
export function getUrlQuery(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}



/**
 * 定时器
 */
export class Timer {

    static timers = {};

    constructor(callback, timeout) {
        this.timer = null;
        this.callback = callback;
        this.timeout = timeout;
    }

    start() {
        if (this.timer) {
            return this.timer;
        }
        return this.timer = setInterval(() => {
            this.callback()
        }, this.timeout);
    }

    stop() {
        clearInterval(this.timer);
        this.timer = null;
    }

    static add(name, callback, timeout) {
        if (Timer.timers[name] != undefined) {
            Timer.timers[name].stop();
        }
        return Timer.timers[name] = new Timer(callback, timeout);
    }

    static stop(name) {
        Timer.timers[name].stop();
    }

    static startAll() {
        for (let k in Timer.timers) {
            Timer.timers[k].start();
        }
    }

    static stopAll() {
        for (let k in Timer.timers) {
            console.log('stop', k)
            Timer.timers[k].stop();
        }
    }
}