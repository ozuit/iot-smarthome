const mqtt = require('mqtt')


const client = mqtt.connect('mqtt://94.237.73.225', {
    username: 'ozuit',
    password: 'ozu@2019',
})

client.subscribe('smarthome/#')
client.on('message', function(topic, payload) {
    const levels = topic.split('/')

    console.log(topic, levels, payload.toString())
})