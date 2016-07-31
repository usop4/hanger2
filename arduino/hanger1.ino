/*
 * 光るハンガー用スケッチ
 * LilyPadを利用
 * シリアルで「01234」と入力すると
 * 1番目のPixelにr=2,g=3,b=4の値が設定される
 * 「00000」と入力すると全てリセットされる
 * 「00777」と入力するとルーレット
 */

#include <Adafruit_NeoPixel.h>
#include <avr/power.h>

// NeoPixel

const int pin = 6;
const int numhangers = 8;
const int interval = 1;
const int numpixels = numhangers * interval;
const int ratio = 1;// 明るさ

const int rouletteMin = numhangers * 1;//
const int rouletteMax = numhangers * 2;//
const int delayTime = 100;

long t = 0;
long old_t = 0;

int r[numhangers];
int g[numhangers];
int b[numhangers];

Adafruit_NeoPixel pixels = Adafruit_NeoPixel(numpixels, pin, NEO_GRB + NEO_KHZ800);

int switchValue;

void setup() {

/*
  pinMode(button1,INPUT);
  digitalWrite(button1,HIGH);
*/
  
  pixels.begin(); 
  
  Serial.begin(115200);
  Serial.println("Hello");

  randomSeed(analogRead(0));

  // 明るさのキャリブレーション用
  for(int i=1;i<numhangers+1;i++){
    liteMyHanger(i,random(9),random(9),random(9));    
  }
  liteMyHanger(1,9,9,9);
  liteMyHanger(numhangers,9,9,9);

}

void loop() {

  t = millis();
  
  int num = 0;
  int n = 0;
  byte buff[4];
  int d1 = 0;
  int d2 = 0;

  while (Serial.available()){
    buff[num] = (int)Serial.read() - 48;
    Serial.print(num);
    Serial.print(" ");
    Serial.println(buff[num]);
    switch(num){
      case 0:
        d1 = buff[num];        
        break;
      case 1:
        d2 = buff[num];        
        n = d1*10+d2;
        break;
      case 2:
        r[n] = buff[num];
        break;
      case 3:
        g[n] = buff[num];
        break;
      case 4:
        b[n] = buff[num];
        liteMyHanger(n,r[n],g[n],b[n]);
        if( r[n]==7 && g[n]==7 && b[n]==7 ){
          roulette();
        }
        Serial.println();
        break;
    }
    num ++;
  }

}

void liteMyHanger(int n,int rn,int gn,int bn){

  r[n] = rn;
  g[n] = gn;
  b[n] = bn;

  if( n == 0 ){
    for(int i=1;i<numhangers+1;i++){
      r[i] = rn;
      g[i] = gn;
      b[i] = bn;
      pixels.setPixelColor( interval*(i-1), pixels.Color(r[i]*ratio,g[i]*ratio,b[i]*ratio));
    }
  }

  pixels.setPixelColor( interval*(n-1), pixels.Color(r[n]*ratio,g[n]*ratio,b[n]*ratio));
  pixels.show();    

}

void roulette(){
  Serial.println("roulette");
  const int imax = random(rouletteMin,rouletteMax);
  for(int i=1; i<imax+1; i++ ){
    liteMyHanger(0,0,0,0);
    if( i%numhangers != 0 ){
      liteMyHanger(i%numhangers,random(9),random(9),random(9));      
    }else{
      liteMyHanger(1,random(9),random(9),random(9));
    }
    delay(delayTime);
  }
  
}
