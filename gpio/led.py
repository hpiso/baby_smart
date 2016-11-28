import RPi.GPIO as GPIO
import sys

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(2,GPIO.OUT)
GPIO.setup(3,GPIO.OUT)
GPIO.setup(4,GPIO.OUT)

state   = sys.argv[1]
ledGPIO = int(sys.argv[2])

if state == "on":
    GPIO.output(ledGPIO, GPIO.HIGH)
elif state == "off":
    GPIO.output(ledGPIO, GPIO.LOW)

