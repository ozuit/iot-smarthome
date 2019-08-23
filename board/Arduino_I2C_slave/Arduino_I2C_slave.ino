#include <Wire.h>

void setup() {
 Wire.begin(8);                /* join i2c bus with address 8 */
 Wire.onRequest(requestEvent); /* register request event */
 Serial.begin(115200);           /* start serial for debug */
}

void loop() {
 delay(100);
 int gas = analogRead(A0);
 Serial.println(gas);
}

// function that executes whenever data is requested from master
void requestEvent() {
 int gas = analogRead(A0);
 char data[3];
 itoa(gas, data, 10);
 Wire.write(data);  /*send string on request */
}
