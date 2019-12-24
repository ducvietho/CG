<?php
namespace App;

class MyConst{
    //request table
    public const NURSER_REQUEST = 1;
    public const PATIENT_REQUEST =2;
    //status request table
    public const REQUESTING = 2;
    public const ACCEPTED =1;
    public const CANCEL =0;
    public const COMPLETED =3;
    //type user
    public const NURSE =1;
    public const PATIENT =2;
    //notification type
    public const NOTI_PATIENT_REQUEST =1;
    public const NOTI_PATIENT_CANCEL =2;
    public const NOTI_PATIENT_ACCEPT = 3;
    public const NOTI_NURSE_REQUEST = 4;
    public const NOTI_NURSE_CANCEL =5;
    public const NOTI_NURSE_ACCEPT =6;
    public const NOTI_COMPLETED =7;
    //type interested
    public const INTERESTED =1;
    //folder certificate
    public const CERTIFICATE = 1;
    public const AVATAR =0;
    //Key noti
    public const SERVER_KEY = "AAAARrj31es:APA91bHt5rD5EDcrzBSYf6cfxs-CIq5u3DVtVngaN9_BBQwuw3-a4sJCAVPF4KWUJKVoBeaOvx7NdM8T2oX8vgeD7KishgbhEfDcXpNFW9jvvHv1al-fo3Op7tqqCzK0cgAXnlmomF8D";
}