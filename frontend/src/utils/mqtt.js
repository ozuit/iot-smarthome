const md5 = require('md5')
const secret = ''

module.exports.signature = function(payload) {
    const ts = parseInt(new Date().getTime() / 1000)
    const data = ts + '|' + payload
    return md5(data + '|' + secret) + '|' + data
}