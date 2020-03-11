const md5 = require('md5')

module.exports.signature = function(payload, secret) {
    const ts = parseInt(new Date().getTime() / 1000)
    const data = ts + '|' + payload
    return md5(data + '|' + secret) + '|' + data
}

module.exports.verify = function(message, secret, timeout = 5) {
    const hash = message.substr(0, 32)
    const ts = message.substr(33, 10)
    const payload = message.substr(44)
    const now_ts = parseInt(new Date().getTime() / 1000)

    // if (now_ts - ts > timeout) {
    //     return false
    // }

    const re_hash = md5(message.substr(33) + '|' + secret)

    if (hash == re_hash) {
        return {
            ts: ts,
            payload: payload
        }
    }

    return false
}