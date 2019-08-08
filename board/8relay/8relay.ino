#include <ESP8266WiFi.h>
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

const char* ssid     = "MINH THU 5";
const char* password = "55555555";
const char* mqtt_server = "94.237.73.225";
const char* secret_key = "";
const int timeout = 5;
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
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("WiFi connected - ESP IP address: ");
  Serial.println(WiFi.localIP());
}

void setup() {

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
 
  Serial.begin(9600);
  setup_wifi();
  timeClient.begin();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);
  
}
 
void loop() {
  timeClient.update();
  
  if(!client.loop()) {  
    client.connect("ESP8266Client");
    client.subscribe("smarthome/#");
    Serial.println("Connected to MQTT Server");
  }

  delay(2000);
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

void callback(char* topic, byte* payload, unsigned int length) {
  String topicStr = topic;
  
  if (verify(payload, length)) {
    String str_payload = String((char*)payload);
    String state = str_payload.substring(44,length);
    Serial.println(topicStr + " " + state);
    
    if (topicStr == "smarthome/living-room/light/device1") // Relay number 8
    {
       if(state == "1") {
          digitalWrite(PIN_D0, HIGH);
       }
       else if (state == "0") {
          digitalWrite(PIN_D0, LOW);
       }
    }
    else if (topicStr == "smarthome/living-room/light/device2") // Relay number 7
    {
       if(state == "1") {
          digitalWrite(PIN_D1, HIGH);
       }
       else if (state == "0") {
          digitalWrite(PIN_D1, LOW);
       }
    }
    else if (topicStr == "smarthome/living-room/fan/device1") // Relay number 6
    {
       if(state == "1") {
          digitalWrite(PIN_D2, HIGH);
       }
       else if (state == "0") {
          digitalWrite(PIN_D2, LOW);
       }
    }
    else if (topicStr == "smarthome/bed-room/light/device1") // Relay number 5
    {
       if(state == "1") {
          digitalWrite(PIN_D3, HIGH);
       }
       else if (state == "0") {
          digitalWrite(PIN_D3, LOW);
       }
    }
    else if (topicStr == "smarthome/bed-room/fan/device1") // Relay number 4
    {
       if(state == "1") {
          digitalWrite(PIN_D4, HIGH);
       }
       else if (state == "0") {
          digitalWrite(PIN_D4, LOW);
       }
    }
    else if (topicStr == "smarthome/bath-room/light/device1") // Relay number 3
    {
       if(state == "1") {
          digitalWrite(PIN_D5, HIGH);
       }
       else if (state == "0") {
          digitalWrite(PIN_D5, LOW);
       }
    }
    else if (topicStr == "smarthome/kitchen/light/device1") // Relay number 2
    {
       if(state == "1") {
          digitalWrite(PIN_D6, HIGH);
       }
       else if (state == "0") {
          digitalWrite(PIN_D6, LOW);
       }
    }
  } else {
    Serial.println("Wrong signature or outdated!");
  }
}
