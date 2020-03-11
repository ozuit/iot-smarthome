#include "DHTesp.h"
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
#include "BH1750FVI.h"
#include "WorkScheduler.h"
#include "Timer.h"

BH1750FVI LightSensor;

#define DHTPIN 16 // D0 on esp8266
#define PIRPIN 13  // D7 on esp8266

const char* mqtt_server = "68.183.234.95";
const char* secret_key = "";
const long utcOffsetInSeconds = 0;
boolean openKitchenLight = false;

// Initializes the espClient
WiFiClient espClient;
PubSubClient client(espClient);
DHTesp dht;

// Define NTP Client to get time
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", utcOffsetInSeconds);

void setup_wifi() {
  delay(10);
  // We start by connecting to a WiFi network
  WiFiManager wifiManager;
  wifiManager.autoConnect("AutoConnectAP");
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

//khởi tạo các job
WorkScheduler *collectDataScheduler;
WorkScheduler *handleDataScheduler;

void collectData() {
  TempAndHumidity measurement = dht.getTempAndHumidity();
 
  Serial.print("Temperature: ");
  Serial.println(measurement.temperature);
 
  Serial.print("Humidity: ");
  Serial.println(measurement.humidity);

  static char temperatureTemp[7];
  dtostrf(measurement.temperature, 6, 2, temperatureTemp);

  static char humidityTemp[7];
  dtostrf(measurement.humidity, 6, 2, humidityTemp);

  client.publish("smarthome/living-room/sensor/temp/sensor1", signature(temperatureTemp));
  client.publish("smarthome/living-room/sensor/hum/sensor1", signature(humidityTemp));

  uint16_t lux = LightSensor.GetLightIntensity();
  Serial.print("Light: ");
  Serial.println(lux);
  float luxFloat = lux;
  static char luxChar[7];
  dtostrf(luxFloat, 6, 2, luxChar);
  client.publish("smarthome/living-room/sensor/light/sensor1", signature(luxChar));
}

void handleData() {
  int lux = LightSensor.GetLightIntensity();

  if (lux < 1) {
    long motionState = digitalRead(PIRPIN);
    if(motionState == HIGH) {
      Serial.println("Motion detected!");
      char *kitchenLight = "1";
      client.publish("smarthome/kitchen/sensor/detection", signature(kitchenLight));
      openKitchenLight = true;
    }
    else {
      Serial.println("Motion absent!");
      if (openKitchenLight) {
        char *kitchenLight = "0";
        client.publish("smarthome/kitchen/sensor/detection", signature(kitchenLight));
        openKitchenLight = false;
      }
    }
  }
}
 
void setup()
{
  Serial.begin(115200);
  setup_wifi();

  // Setup temperature sensor
  dht.setup(DHTPIN, DHTesp::DHT22);
  // Setup motion sensor
  pinMode(PIRPIN, INPUT);
  // Setup light sensor
  LightSensor.begin();
  LightSensor.SetAddress(Device_Address_H);
  LightSensor.SetMode(Continuous_H_resolution_Mode);
  
  timeClient.begin();
  client.setServer(mqtt_server, 1883);

  // Initial class timer (design pattern singleton)
  Timer::getInstance()->initialize();
  // Initial jobs
  collectDataScheduler = new WorkScheduler(10000UL, collectData);
  handleDataScheduler = new WorkScheduler(1000UL, handleData);
}
 
void loop()
{
  timeClient.update();
  
  if(!client.loop()) {
    client.connect("ESP8266Sensors");
  }

  Timer::getInstance()->update();
  collectDataScheduler->update();
  handleDataScheduler->update();
  Timer::getInstance()->resetTick();
}
