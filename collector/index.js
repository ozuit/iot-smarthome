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
                    // Send Notify
                    request.get('https://maker.ifttt.com/trigger/gas_warning/with/key/bkK2wFkIFiUqGRoMCGxfmH')
                    mysql_con.query('UPDATE node SET active = 0 WHERE is_sensor = 0', function (error, results, fields) {
                        if (error) console.error(error)
                    });
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