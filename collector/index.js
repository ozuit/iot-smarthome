const mqtt = require('mqtt')
const util = require('./util')
const secret_key = process.env.MQTT_SECRET_KEY || ''
const mysql = require('mysql')
let sensorMapTable = {}

const mysql_con = mysql.createConnection({
    host: "127.0.0.1",
    user: "root",
    password: "password",
    database: "iot"
});

const client = mqtt.connect('mqtt://94.237.73.225', {
    username: 'ozuit',
    password: 'ozu@2019',
})

const refreshSensorMapTable = function(cb) {
    sensorMapTable = {}

    let sql = `SELECT * FROM sensor`
    mysql_con.query(sql, function (err, result) {
        if (err) throw err;
        
        result.forEach(function(row, idx) {
            sensorMapTable[row.topic] = row.id
        })
console.log(sensorMapTable)
        cb();
    });
}

const registerMQTT = function() {
    client.subscribe('smarthome/#')
    client.on('message', function(topic, message) {
        const levels = topic.split('/')

        if (result = util.verify(message.toString(), secret_key)) {
            const record = {sensor_id: sensorMapTable[topic], topic: topic, value: parseFloat(result.payload)}
            mysql_con.query('INSERT INTO data SET ?', record, function (error, results, fields) {
                if (error) console.error(error)
            });
        } else {
            console.error('Wrong signature or outdated!');
        }
    })
}

mysql_con.connect(function(err) {
    if (err) throw err;

    refreshSensorMapTable(registerMQTT);
    setInterval(refreshSensorMapTable, 60000);
});