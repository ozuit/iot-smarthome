#include <ESP8266WiFi.h>
#include <DNSServer.h>
#include <ESP8266WebServer.h>
#include <WiFiManager.h>
#include <PubSubClient.h>
#include "MD5.h"
#include <stdio.h> 
#include <time.h>
#include <stdlib.h>
#include <WiFiUdp.h>
#include <NTPClient.h>
#include <Wire.h>
#include "WorkScheduler.h"
#include "Timer.h"

#define PIN_D0  16
#define PIN_D1  5
#define PIN_D2  4
#define PIN_D3  0
#define PIN_D4  2
#define PIN_D5  14
#define PIN_D6  12
#define PIN_D7  13
#define PIN_D8  15

const char* mqtt_server = "68.183.234.95";
const char* secret_key = "";
const int timeout = 3;
const long utcOffsetInSeconds = 0;
boolean gasWarning = false;

//khởi tạo các job
WorkScheduler *collectDataScheduler;
WorkScheduler *handleDataScheduler;

// Initializes the espClient
WiFiClient espClient;
PubSubClient client(espClient);

// Define NTP Client to get time
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", utcOffsetInSeconds);

void setup_wifi() {
  delay(10);
  // We start by connecting to a WiFi network
  WiFiManager wifiManager;
  wifiManager.autoConnect("AutoConnectAP");
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Attempt to connect
    if (client.connect("ESP8266Devices")) {
      Serial.println("Connected to MQTT Server");
      // ... and subscribe to topic
      client.subscribe("smarthome/living-room/light/#");
      client.subscribe("smarthome/living-room/fan/#");
      client.subscribe("smarthome/bed-room/light/#");
      client.subscribe("smarthome/bed-room/fan/#");
      client.subscribe("smarthome/bath-room/light/#");
      client.subscribe("smarthome/kitchen/light/#");
    } else {
      Serial.print("Connect failed");
      Serial.println("Try again in 1 seconds");
      // Wait 1 seconds before retrying
      delay(1000);
    }
  }
}

char* signature(char* payload)
{
  unsigned long ts = timeClient.getEpochTime();

  String str_payload = String(payload);
  str_payload.trim();
  String hash_data = String(ts) + '|' + str_payload + '|' + String(secret_key);

  char * cstr = new char [hash_data.length()+1];
  strcpy (cstr, hash_data.c_str());

  unsigned char* hash = MD5::make_hash(cstr);
  char *md5str = MD5::make_digest(hash, 16);

  String signed_payload = String(md5str) + '|' + String(ts) + '|' + str_payload;
  char * cstr_signed = new char [signed_payload.length()+1];
  strcpy (cstr_signed, signed_payload.c_str());
  
  return cstr_signed;
}

void collectData() {
  Wire.requestFrom(8, 3); /* request & read data of size 3 from slave */
  char gas[3];
  for (int i = 0; i < 3; i++){
     gas[i] = Wire.read();
  }
  Serial.print("Gas: ");
  int gasInt = atoi(gas);
  char gasChar[3];
  itoa(gasInt, gasChar, 10);
  Serial.println(gasChar);
  if (gasInt > 500 && gasWarning == false) {
    gasWarning = true;
    client.publish("smarthome/kitchen/sensor/gas/sensor1", signature(gasChar));
  } else {
    gasWarning = false;
  }
}

void handleData() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();
}

void setup() {
  Serial.begin(115200);
  Wire.begin(PIN_D1, PIN_D2); /* join i2c bus with SDA=D1 and SCL=D2 of NodeMCU */
  setup_wifi();

  pinMode(PIN_D0, OUTPUT);
  digitalWrite(PIN_D0, LOW);
  
  pinMode(PIN_D3, OUTPUT);
  digitalWrite(PIN_D3, LOW);
  
  pinMode(PIN_D4, OUTPUT);
  digitalWrite(PIN_D4, LOW);
  
  pinMode(PIN_D5, OUTPUT);
  digitalWrite(PIN_D5, LOW);
  
  pinMode(PIN_D6, OUTPUT);
  digitalWrite(PIN_D6, LOW);
  
  pinMode(PIN_D7, OUTPUT);
  digitalWrite(PIN_D7, LOW);

  pinMode(PIN_D8, OUTPUT);
  digitalWrite(PIN_D8, LOW);
 
  timeClient.begin();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);

  // Initial class timer (design pattern singleton)
  Timer::getInstance()->initialize();
  // Initial jobs
  collectDataScheduler = new WorkScheduler(1000UL, collectData);
  handleDataScheduler = new WorkScheduler(100UL, handleData);
}
 
void loop() {
  timeClient.update();

  Timer::getInstance()->update();
//  collectDataScheduler->update();
  handleDataScheduler->update();
  Timer::getInstance()->resetTick();
}

boolean verify(byte* payload, unsigned int length)
{
  unsigned long now_ts = timeClient.getEpochTime();
  String str_payload = String((char*)payload);
  String hash = str_payload.substring(0,32);
  String ts = str_payload.substring(33,43);
  unsigned long ts_long = ts.toInt();

  if (abs(now_ts - ts_long) > timeout) {
      Serial.println("Outdated!");
      return false;
  }

  String hash_data = str_payload.substring(33, length) + '|' + secret_key;
  char *cstr = new char [hash_data.length()+1];
  strcpy (cstr, hash_data.c_str());
  unsigned char* re_hash = MD5::make_hash(cstr);
  char *md5str = MD5::make_digest(re_hash, 16);
  
  if (hash == String(md5str)) {
    return true;
  }
  Serial.println("Wrong signature!");
  return false;
}

void handleDevice(String state, int PIN) {
  if(state == "1") {
      digitalWrite(PIN, HIGH);
   }
   else if (state == "0") {
      digitalWrite(PIN, LOW);
   }
}

void callback(char* topic, byte* payload, unsigned int length) {
  String topicStr = topic;
  
  if (verify(payload, length)) {
    String str_payload = String((char*)payload);
    String state = str_payload.substring(44,length);
    Serial.println(topicStr + " " + state);
    
    if (topicStr == "smarthome/living-room/light/device1") // Relay number 8
    {
       handleDevice(state, PIN_D0);
    }
    else if (topicStr == "smarthome/living-room/fan/device1") // Relay number 6
    {
       handleDevice(state, PIN_D8);
    }
    else if (topicStr == "smarthome/bed-room/light/device1") // Relay number 5
    {
       handleDevice(state, PIN_D3);
    }
    else if (topicStr == "smarthome/bed-room/fan/device1") // Relay number 4
    {
       handleDevice(state, PIN_D4);
    }
    else if (topicStr == "smarthome/bath-room/light/device1") // Relay number 3
    {
       handleDevice(state, PIN_D5);
    }
    else if (topicStr == "smarthome/kitchen/light/device1") // Relay number 2
    {
       handleDevice(state, PIN_D6);
    }
    else if (topicStr == "smarthome/living-room/light/device2") // Relay number 1
    {
       handleDevice(state, PIN_D7);
    }
  }
}
