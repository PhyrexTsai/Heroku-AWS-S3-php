FileUpload
這邊會透過 AWS 去抓出現在有多少個 bucket 被建置了，並且可以選擇想要上傳至哪一個 bucket
上傳完成後會存一份資料到 redis 內與同步上傳到 S3
同時上傳的時候會判斷是否有 imagelist.txt 檔案，所有檔案的名稱與對應資料都會存在這邊
imagelist.txt 存在指定的 bucket 內

FileDownload
這邊會去 S3 抓 imagelist.txt 來比對現在在 S3 的檔案清單
如果 redis 內有資料就會將 redis 內的 base64 格式顯示出來
如果沒有則會去指向 S3 的檔案

Github
https://github.com/PhyrexTsai/Heroku-AWS-S3-php