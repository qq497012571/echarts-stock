

function MA(close, dayCount) {
    var result = [];
    for (var i = 0, len = close.length; i < len; i++) {
        if (i < dayCount) {
            result.push("-");
            continue;
        }
        var sum = 0;
        for (var j = 0; j < dayCount; j++) {
            sum += parseFloat(close[i - j]);
        }

        result.push((sum / dayCount).toFixed(2));
    }
    return result;
}


function calculateEMA(values, days) {
    let k = 2 / (days + 1);
    let ema = [];
    let sma = 0;
    for (let i = 0; i < values.length; i++) {
        if (i < days - 1) {
            sma += values[i];
            ema.push(null);
        } else if (i === days - 1) {
            sma += values[i];
            ema.push(sma / days);
        } else {
            let prevEma = ema[i - 1];
            let currValue = values[i];
            let currEma = (currValue - prevEma) * k + prevEma;
            ema.push(currEma);
        }
    }
    return ema;
}

function calculateMACD(values) {
    let ema12 = calculateEMA(values, 12);
    let ema26 = calculateEMA(values, 26);
    let dif = [];
    for (let i = 0; i < values.length; i++) {
        let currDif = ema12[i] - ema26[i];
        dif.push(currDif);
    }
    let dea = calculateEMA(dif, 9);
    let macd = [];
    for (let i = 0; i < values.length; i++) {
        let currMacd = (dif[i] - dea[i]) * 2;
        macd.push(currMacd.toFixed(2));
    }
    return { dif, dea, macd };
}
