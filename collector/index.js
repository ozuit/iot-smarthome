const mqtt = require('mqtt')
const util = require('./util')
const secret_key = process.env.MQTT_SECRET_KEY || '';

const client = mqtt.connect('mqtt://94.237.73.225', {
    username: 'ozuit',
    password: 'ozu@2019',
})

client.subscribe('smarthome/#')
client.on('message', function(topic, message) {
    const levels = topic.split('/')

    console.log(topic, levels, message.toString())

    if (result = util.verify(message.toString(), secret_key)) {
        console.log(result)
    } else {
        console.error('Wrong signature or outdated!');
    }
})