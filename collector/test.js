const mqtt = require('mqtt')
const util = require('./util')

const secret_key = process.env.MQTT_SECRET_KEY || '';
const client = mqtt.connect('mqtt://68.183.234.95')

function testSensorData() {
    setInterval(function() {
        // motion
        // const payload1 = util.signature(Math.random() < 0.5 ? 0 : 1, secret_key)
        // console.log(payload1)
        // client.publish('smarthome/living-room/sensor/motion/sensor1', payload1)
        
        // temperature
        const payload2 = util.signature(Math.round((Math.random()*20 + 20)*10)/10, secret_key)
        console.log(payload2)
        client.publish('smarthome/bed-room/sensor/temp/sensor1', payload2)
        
        // humidity
        const payload3 = util.signature(Math.round((Math.random()*50 + 50)*10)/10, secret_key)
        console.log(payload3)
        client.publish('smarthome/bed-room/sensor/hum/sensor1', payload3)
        
        // light
        // const lux = Math.round(Math.random()*1000)
        // const lux_min = Math.round(Math.random()*1000)
        // const payload4 = util.signature((Math.round(lux / lux_min)*1000)/1000, secret_key)
        // console.log(payload4)
        // client.publish('smarthome/living-room/sensor/light/sensor1', payload4)
    }, 1000);
}

function testDeviceData() {
    let state = '1';

    setInterval(function() {
        const living_room_light_1 = util.signature(state, secret_key)
        console.log(living_room_light_1)
        client.publish('smarthome/living-room/light/device1', living_room_light_1)
        state = state == '0' ? '1' : '0';
    }, 3000);
}

client.on('connect', function () {
    // testSensorData()
    // testDeviceData()
    const payload3 = util.signature('1', secret_key)
    console.log(payload3)
    client.publish('smarthome/kitchen/sensor/detection', payload3)
})