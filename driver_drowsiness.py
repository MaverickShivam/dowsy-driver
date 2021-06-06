import cv2
import numpy as np
import dlib
from imutils import face_utils
from playsound import playsound
import time
import requests

cap = cv2.VideoCapture(0)

detector = dlib.get_frontal_face_detector()
predictor = dlib.shape_predictor("shape_predictor_68_face_landmarks.dat")

sleep = 0
drowsy = 0
active = 0
status=""
color=(0,0,0)

def compute(ptA,ptB):
    dist = np.linalg.norm(ptA - ptB)
    return dist

def blinked(a,b,c,d,e,f):
    up = compute(b,d) + compute(c,e)
    down = compute(a,f)
    ratio = up/(2.0*down)

    
    if(ratio>0.25):
        return 2
    elif(ratio>0.21 and ratio<=0.25):
        return 1
    else:
        return 0
def savefile(filename,data):
    f = open(filename, "w")
    f.write(data)
    f.close()

while True:
    try:
        frame = cv2.imread("image.png")
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    except:
        continue

    faces = detector(gray)
    for face in faces:
        x1 = face.left()
        y1 = face.top()
        x2 = face.right()
        y2 = face.bottom()

        face_frame = frame.copy()
        cv2.rectangle(face_frame, (x1, y1), (x2, y2), (0, 255, 0), 2)

        landmarks = predictor(gray, face)
        landmarks = face_utils.shape_to_np(landmarks)

        left_blink = blinked(landmarks[36],landmarks[37], 
        	landmarks[38], landmarks[41], landmarks[40], landmarks[39])
        right_blink = blinked(landmarks[42],landmarks[43], 
        	landmarks[44], landmarks[47], landmarks[46], landmarks[45])

        if(left_blink==0 or right_blink==0):
            sleep+=1
            drowsy=0
            active=0
            if(sleep>30 and sleep<60):
                status="SLEEPING !!!"
                color = (80,80,255)
            if(sleep>60 and sleep<90):
                playsound('beep-1.mp3')
                status="Calling police/boss in "+str(int((90-sleep)/2))+" seconds"; 
            if(sleep==90):
                status="you are gone now"
                url = "https://www.fast2sms.com/dev/bulkV2"
                payload = "message=Driver%20in%20KA20GC0516%20is%20sleeping&language=english&route=q&numbers=7691027248,6362681118"
                headers = {
                    'authorization': "dfIRBEy0TGASaFxCioOQqg3P9NcLmjps8ukXzKt2Vw5nl61hYeiDnBQYRNto1PGAMuUmIzHphsEW2xKd",
                    'Content-Type': "application/x-www-form-urlencoded",
                    'Cache-Control': "no-cache",
                    }

                response = requests.request("POST", url, data=payload, headers=headers)

            if(sleep>90):
                playsound("beep-2.mp3")

        elif(left_blink==1 or right_blink==1):
            sleep=0
            active=0
            drowsy+=1
            if(drowsy>6):
                status="Drowsy !"
                color = (0,0,255)

        else:
            drowsy=0
            sleep=0
            active+=1
            if(active>10):
                status="Active :)"
                color = (0,255,0)
        print(status," : ",color)
        savefile("status.text",status)
        savefile("color.text",str(color))
        cv2.putText(frame, status, (100,100), cv2.FONT_HERSHEY_SIMPLEX, 0.8, color,1)

        for n in range(0, 68):
            (x,y) = landmarks[n]
            cv2.circle(face_frame, (x, y), 1, (255, 255, 255), -1)

    #cv2.imshow("Frame", frame)
    #cv2.imshow("Result of detector", face_frame)
    key = cv2.waitKey(1)
    if key == 27:
      	break
