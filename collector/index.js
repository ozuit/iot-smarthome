const mqtt = require('mqtt')
const util = require('./util')
const mysql = require('mysql')
const dotenv = require('dotenv');
const request = require('request');
dotenv.config();
const secret_key = process.env.MQTT_SECRET_KEY || ''
const maxGas = 500;
let nodeMapTable = {}

const mysql_con = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME
});

const client = mqtt.connect('mqtt://94.237.73.225')

const refreshSensorMapTable = function(cb) {
    nodeMapTable = {}

    let sql = `SELECT * FROM node`
    mysql_con.query(sql, function (err, result) {
        if (err) throw err;
        
        result.forEach(function(row, idx) {
            nodeMapTable[row.topic] = row.id
        })

        if (cb) cb();
    });
}

const registerMQTT = function() {
    client.subscribe('smarthome/+/sensor/#')
    client.on('message', function(topic, message) {
        if (result = util.verify(message.toString(), secret_key)) {
            if (topic == 'smarthome/kitchen/sensor/gas/sensor1') {
                if (parseFloat(result.payload) > maxGas) {
                    // Turn off all devices
                    client.publish('smarthome/bed-room/fan/device1', util.signature(0, secret_key))
                    client.publish('smarthome/kitchen/light/device1', util.signature(0, secret_key))
                    client.publish('smarthome/bath-room/light/device1', util.signature(0, secret_key))
                    client.publish('smarthome/bed-room/light/device1', util.signature(0, secret_key))
                    client.publish('smarthome/living-room/fan/device1', util.signature(0, secret_key))
                    client.publish('smarthome/living-room/light/device2', util.signature(0, secret_key))
                    client.publish('smarthome/living-room/light/device1', util.signature(0, secret_key))

                    // Send SMS
                    request.get('https://maker.ifttt.com/trigger/gas_warning/with/key/bkK2wFkIFiUqGRoMCGxfmH')
                }
            } else {
                const record = {node_id: nodeMapTable[topic], topic: topic, value: parseFloat(result.payload)}
                console.log(record)
                mysql_con.query('INSERT INTO data SET ?', record, function (error, results, fields) {
                    if (error) console.error(error)
                });
            }
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