const mqtt = require('mqtt')


const client = mqtt.connect('mqtt://94.237.73.225', {
    username: 'ozuit',
    password: 'ozu@2019',
})

client.subscribe('/smart-home')
client.on('message', function(topic, payload) {
    console.log(topic, payload.toString());
})