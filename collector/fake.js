const mysql = require('mysql')
const moment = require('moment')
const momentRandom = require('moment-random')
const dotenv = require('dotenv')
dotenv.config()

let nodeMapTable = {}

const mysql_con = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME
});

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

mysql_con.connect(function(err) {
    if (err) throw err;

    refreshSensorMapTable(fakeData);
});

const insertTable = function(topic, value, created_at) {
    const record = {node_id: nodeMapTable[topic], topic: topic, value: value, created_at: created_at}
    mysql_con.query('INSERT INTO data SET ?', record, function (error, results, fields) {
        if (error) console.error(error)
    });
}

const randomSensorData = function(min, max) {
    return Math.round((Math.random()*min + (max - min))*10)/10
}

const randomData = function(date) {
    let start = null
    let end = null

    // 6h30 - 7h thức dậy => tắt đèn ngủ
    start = date + ' 06:30:00'
    end = date + ' 07:00:00'
    insertTable('smarthome/bed-room/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 7h - 7h30 làm vệ sinh cá nhân => mở đèn phòng tắm
    start = date + ' 07:00:00'
    end = date + ' 07:30:00'
    insertTable('smarthome/bath-room/light/device1', 1, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 7h30 - 8h ăn sáng => tắt đèn phòng tắm
    start = date + ' 07:30:00'
    end = date + ' 08:00:00'
    insertTable('smarthome/bath-room/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 8h - 18h30 đi làm => tắt hết tất cả thiết bị
    start = date + ' 07:30:00'
    end = date + ' 08:00:00'
    insertTable('smarthome/bed-room/fan/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/kitchen/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/bath-room/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/bed-room/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/living-room/fan/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/living-room/light/device2', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/living-room/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 18h30 - 19h về tắm rửa => mở 1 đèn phòng khách, đèn bếp, đèn phòng tắm
    start = date + ' 18:30:00'
    end = date + ' 19:00:00'
    insertTable('smarthome/living-room/light/device1', 1, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/kitchen/light/device1', 1, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/bath-room/light/device1', 1, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 19h - 19h30 tắm xong, ăn tối => tắt đèn phòng tắm
    start = date + ' 19:00:00'
    end = date + ' 19:30:00'
    insertTable('smarthome/bath-room/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 19h30 - 21h30 xem TV, giải trí phòng khách => mở quạt phòng khách + tắt đèn bếp
    start = date + ' 19:30:00'
    end = date + ' 21:30:00'
    insertTable('smarthome/living-room/fan/device1', 1, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/kitchen/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 21h30 - 22h30 đọc sách => mở thêm 1 đèn phòng khách
    start = date + ' 21:30:00'
    end = date + ' 22:30:00'
    insertTable('smarthome/living-room/sensor/light/sensor1', randomSensorData(40, 50), momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/living-room/light/device2', 1, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 22h30 - 23h vào phòng ngủ => tắt tất cả đèn, quạt phòng khách + mở đèn ngủ + quạt ngủ
    start = date + ' 22:30:00'
    end = date + ' 23:00:00'
    insertTable('smarthome/living-room/fan/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/living-room/light/device2', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/living-room/light/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))

    // 2h-3h + nhiệt độ 24 -> 28 độ => tắt quạt ngủ
    start = date + ' 02:00:00'
    end = date + ' 03:00:00'
    insertTable('smarthome/bed-room/sensor/temp/sensor1', randomSensorData(24, 28), momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
    insertTable('smarthome/bed-room/fan/device1', 0, momentRandom(end, start).format('YYYY-MM-DD hh:mm:ss'))
}

const fakeData = function() {
    const start = moment().startOf('year')
    const end   = moment().endOf('year')
    
    while(start <= end) {
        randomData(start.format('YYYY-MM-DD'))
        start.add(1, 'days')
    }
}