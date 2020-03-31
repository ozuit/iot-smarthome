const mqtt = require('mqtt')
const moment = require('moment')
const util = require('./util')
const mysql = require('mysql')
const dotenv = require('dotenv');
const axios = require('axios')
const request = require('request');

dotenv.config();
const secret_key = process.env.MQTT_SECRET_KEY || ''
const maxGas = 500;
let nodeMapTable = {}
let settingMapTable = {}
const axiosInstance = axios.create({
    baseURL: process.env.API_ENPOINT,
});

const mysql_con = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME
});

const client = mqtt.connect(`mqtt://${process.env.MQTT_SERVER}`)

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

const refreshSettingMapTable = function() {
    settingMapTable = {}

    let sql = `SELECT * FROM setting`
    mysql_con.query(sql, function (err, result) {
        if (err) throw err;
        
        settingMapTable['active_fan_sensor'] = result[0].active_fan_sensor;
        settingMapTable['limit_fan_sensor'] = result[0].limit_fan_sensor;
        settingMapTable['active_motion_detection'] = result[0].active_motion_detection;
    });
}

const registerMQTT = function() {
    client.subscribe('smarthome/+/sensor/#')
    client.on('message', function(topic, message) {
        if (result = util.verify(message.toString(), secret_key)) {
            if (topic == 'smarthome/kitchen/sensor/gas/sensor1' && settingMapTable['active_gas_warning'] == 1) {
                if (parseFloat(result.payload) > maxGas) {
                    axiosInstance.put(`/api/${process.env.INTERNAL_TOKEN}/iot-agent/turn-off-all`)
                    // Send Notify
                    request.get('https://maker.ifttt.com/trigger/gas_warning/with/key/bkK2wFkIFiUqGRoMCGxfmH')
                }
            }
            else if (topic == 'smarthome/kitchen/sensor/detection' && settingMapTable['active_motion_detection'] == 1) {
                axiosInstance.put(`/api/${process.env.INTERNAL_TOKEN}/node/update`, {
                    topic: 'smarthome/kitchen/light/device1',
                    status: result.payload
                })
            }
            else {
                if ((topic == 'smarthome/living-room/sensor/temp/sensor1') && (parseFloat(result.payload) > settingMapTable['limit_fan_sensor']) && settingMapTable['active_fan_sensor'] == 1) {
                    // Turn on fan
                    mysql_con.query(`SELECT * FROM node WHERE id = ${nodeMapTable['smarthome/living-room/fan/device1']}`, function (error, results) {
                        if (error) console.error(error)

                        if (results[0].active === 0 && (moment().diff(moment(results[0].updated_at), 'hours') >= 2)) {
                        // if (results[0].active === 0) {
                            const living_room_fan_on = util.signature('1', secret_key)
                            client.publish('smarthome/living-room/fan/device1', living_room_fan_on)
                            mysql_con.query(`UPDATE node SET active = 1 WHERE id = ${nodeMapTable['smarthome/living-room/fan/device1']}`, function (error, results, fields) {
                                if (error) console.error(error)
                            });
                        } 
                    });
                }
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

    refreshSettingMapTable();
    setInterval(refreshSettingMapTable, 5000);
});