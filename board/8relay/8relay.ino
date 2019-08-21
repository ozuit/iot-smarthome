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

#define PIN_D0  16 // Led 8
#define PIN_D1  5 // Led 7
#define PIN_D2  4 // Led 6
#define PIN_D3  0 // Led 5
#define PIN_D4  2 // Led 4
#define PIN_D5  14 // Led 3
#define PIN_D6  12 // Led 2
#define PIN_D7  13 // Led 1

const char* mqtt_server = "94.237.73.225";
const char* secret_key = "";
const int timeout = 20;
const long utcOffsetInSeconds = 0;

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

void setup() {
  Serial.begin(115200);
  setup_wifi();

  pinMode(PIN_D0, OUTPUT);
  digitalWrite(PIN_D0, LOW);
  
  pinMode(PIN_D1, OUTPUT);
  digitalWrite(PIN_D1, LOW);
  
  pinMode(PIN_D2, OUTPUT);
  digitalWrite(PIN_D2, LOW);
  
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
 
  timeClient.begin();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);
  
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
      Serial.println("Try again in 2 seconds");
      // Wait 2 seconds before retrying
      delay(2000);
    }
  }
}
 
void loop() {
  timeClient.update();

  if (!client.connected()) {
    reconnect();
  }
  client.loop();
}

boolean verify(byte* payload, unsigned int length)
{
  unsigned long now_ts = timeClient.getEpochTime();
  String str_payload = String((char*)payload);
  String hash = str_payload.substring(0,32);
  String ts = str_payload.substring(33,43);
  unsigned long ts_long = ts.toInt();

  if (now_ts - ts_long > timeout) {
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
    else if (topicStr == "smarthome/living-room/light/device2") // Relay number 1
    {
       handleDevice(state, PIN_D7);
    }
    else if (topicStr == "smarthome/living-room/fan/device1") // Relay number 6
    {
       handleDevice(state, PIN_D2);
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
  } else {
    Serial.println("Wrong signature or outdated!");
  }
}
