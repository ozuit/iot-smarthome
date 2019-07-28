const mqtt = require('mqtt')
const util = require('./util')

const client = mqtt.connect('mqtt://94.237.73.225', {
    username: 'ozuit',
    password: 'ozu@2019',
})

client.on('connect', function () {
    setInterval(function() {
        // motion
        const payload1 = util.signature(Math.random() < 0.5 ? 0 : 1)
        console.log(payload1)
        client.publish('smarthome/living-room/sensor/motion/sensor1', payload1)
        
        // temperature
        const payload2 = util.signature(Math.round((Math.random()*20 + 20)*10)/10)
        console.log(payload2)
        client.publish('smarthome/living-room/sensor/temp/sensor1', payload2)
        
        // humidity
        const payload3 = util.signature(Math.round((Math.random()*50 + 50)*10)/10)
        console.log(payload3)
        client.publish('smarthome/living-room/sensor/hum/sensor1', payload3)
        
        // light
        const lux = Math.round(Math.random()*1000)
        const lux_min = Math.round(Math.random()*1000)
        const payload4 = util.signature((Math.round(lux / lux_min)*1000)/1000)
        console.log(payload4)
        client.publish('smarthome/living-room/sensor/light/sensor1', payload4)
    }, 1000);
})