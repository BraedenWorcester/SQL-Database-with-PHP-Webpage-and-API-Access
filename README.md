# SQL Database with PHP Webpage and API Access

This repository contains my PHP code for facilitating interaction with an SQL database, hosted on an EC2 instance. There are two methods in which a user may interact with the database: a simple webpage designed for ease of human use, and the API.

# Schema

The schema for the database is relatively simple. There is a 'devices' table where each row represents a single device with 5 columns: device_id (auto incrementing ID), type (laptop, phone, car, etc), brand (microsoft, apple, toyota, etc), serial_number, and status (active or inactive). In addition, there is a 'device_file_paths' table where each row represents a PDF file with 3 columns: file_id (auto incrementing ID), file_path, and device_id (foreign key representing the device with which this PDF is associated). 

# Webpage

The webpage can be accessed by visiting the server address in a browser, https://ec2-3-91-97-58.compute-1.amazonaws.com/ (the certificate has long since expired so your browser will probably complain, there are some screenshots below if you don't feel comfortable visiting the site directly). My main goal was to provide a clear interface with which a human can perform data queries that would otherwise require handtyped (and potentially dangerous) SQL commands. I did my best to sanitize all the input to prevent SQL injection, but I also think it'd be really funny if someone managed to do it anyways, so you have my blessing to try and wreck the database.

Screengrab of search query GUI:

![Screenshot (604)](https://user-images.githubusercontent.com/56178051/172810947-251f33fd-4d96-417a-9e02-38597df3b6c2.png)

Output:

![Screenshot (605)](https://user-images.githubusercontent.com/56178051/172811424-e501366f-57f0-4a24-8957-05db3d471bdb.png)

Manage Device Files:

![Screenshot (606)](https://user-images.githubusercontent.com/56178051/172814798-39268721-a4db-48d7-a3d0-440582c9da82.png)

Manage Device Files Contd:

![Screenshot (607)](https://user-images.githubusercontent.com/56178051/172814912-2c0d71fb-55ec-42f7-9a5e-31ad787de75d.png)


Other options available on site: add device, modify device (change serial number, brand, and/or type), and delete device.

# API

The API is accessed through the server address. List of commands below (all output is in JSON, newlines are for demonstration and not present in actual output):

**/search**

 _requires at least one search condition:_
  
    POST serial_number
    POST brand
    POST type
    POST status (valid inputs: "active", "inactive")
   
_optional:_

    POST exact (valid inputs: "true", "false" - defaults to "false" - determines whether query uses relation 'LIKE' (false) or '=' (true) )
    POST return_columns (valid input: comma deliminated list of columns - defaults to returning every column)
    
_output:_

    {
    "num_rows": $number_of_returned_rows_int,
    $device_id_1_int{$column_name_1_str:$column_value_1_str,$column_name_2_str:$column_value_2_str,$column_name_3_str:$column_value_3_str,...},
    $device_id_2_int{$column_name_1_str:$column_value_1_str,$column_name_2_str:$column_value_2_str,$column_name_3_str:$column_value_3_str,...},
    $device_id_3_int{$column_name_1_str:$column_value_1_str,$column_name_2_str:$column_value_2_str,$column_name_3_str:$column_value_3_str,...},
    ...
    }

_sample curl:_

    curl -k https://ec2-3-91-97-58.compute-1.amazonaws.com/search/ -d "brand=microsoft&type=laptop" -d "exact=true&return_columns=serial_number,status"
  
  
**/modify-device**

 _requires at least one identifier:_
 
     POST serial_number
     POST device_id
     
_requires at least one change:_

    POST new_type (changes device type - ex: 'new_type=laptop' will change device type to 'laptop')
    POST new_serial_number (changes device SN - ex: 'new_serial_number=newnumber' will change device SN to 'newnumber')
    POST new_brand (changes device brand - ex: 'new_brand=microsoft' will change device brand to 'microsoft')
    POST new_status (valid inputs: "active", "inactive" - changes device status - ex: 'new_status=active' will change device status to 'active')
    
_output - successful change:_

    ["success: device modified"]
   
_output - serial_number not found:_

    ["error: device with serial_number '$identifying_serial_number_input_str' not found"]
    
_output - device_id not found:_

    ["error: device with device_id '$identifying_device_id_input_str' not found"]

_output - missing serial_number or device_id:_

    ["error: require input for at least one of: device_id, serial_number"]
    
_output - no changes specified:_

    ["error: require input for at least one of: new_serial_number, new_brand, new_type, new_status"]

_sample curl:_

    curl -k https://ec2-3-91-97-58.compute-1.amazonaws.com/modify-device/ -d "serial_number=c39ad28ab13a9093439670d89c0a1140" -d "new_brand=TwoPlus&new_type=stationary phone"


**/add-device**

_required device initializers:_

    POST serial_number
    POST brand
    POST type
    
_optional device initializer:_

    POST status (valid inputs: "active", "inactive" - defaults to inactive w/ warning in output)
    
_output - successful:_

    ["success: device added"]
    
_output - successful w/ default status:_

    ["success: device added", "warning: status defaulted to inactive"]
    
_output - missing serial_number:_

    ["error: require input for serial_number"]
    
_output - missing brand:_

    ["error: require input for brand"]
    
_output - missing type:_

    ["error: require input for type"]

_sample curl:_

    curl -k https://ec2-3-91-97-58.compute-1.amazonaws.com/add-device/ -d "serial_number=newnumber&brand=newbrand&type=newtype"
    
**/remove-device**

 _requires at least one identifier:_
 
     POST serial_number
     POST device_id
     
 _output - successful removal:_

    ["success: device is removed"]
   
_output - serial_number not found:_

    ["error: device with serial_number '$identifying_serial_number_input_str' not found"]
    
_output - device_id not found:_

    ["error: device with device_id '$identifying_device_id_input_str' not found"]
    
_sample curl:_

    curl -k https://ec2-3-91-97-58.compute-1.amazonaws.com/remove-device/ -d "serial_number= testnumber"
    
    
**/manage-device-files**

 _requires at least one identifier:_
 
     POST serial_number
     POST device_id
     
 _requires operation (only one operation will be executed - operations are listed in descending precedence):_
 
     POST download_file (comma deliminated list of files to download)
     POST delete_file (comma deliminated list of files to delete)
     FILE $file_path_to_upload_str (file to upload - if there are 'i' duplicate files, '-i' is appended to basename, ex: test3.pdf w/ 3 duplicates is test3-3.pdf - may upload multiple files)
     
_output - download:_

    .zip file w/ found files
    
_output - removal [success, fail, possible fail]:_

    [
    "successful delete: $file_to_delete_input_1_str", 
    "failed delete: $file_to_delete_input_2_str (SQL failure)", 
    "possible failed delete: $file_to_delete_input_3_str (failed to unlink - file may not exist)"
    ]
    
_output - upload [success, fail]:_

    [
    "successful delete: $file_to_upload_input_1_str", 
    "failed delete: $file_to_upload_input_2_str (SQL failure)"
    ]
    
_sample download curl:_

    curl -k https://ec2-3-91-97-58.compute-1.amazonaws.com/manage-device-files/ -d "serial_number=db4734761a9d5a8d1f98d9e7fc383996" -d "download_file=file1,file2,file3"
    
_sample delete curl:_

    curl -k https://ec2-3-91-97-58.compute-1.amazonaws.com/manage-device-files/ -d "serial_number=db4734761a9d5a8d1f98d9e7fc383996" -d "delete_file=file1,file2,file3"
    
_sample upload curl:_

    curl -k https://ec2-3-91-97-58.compute-1.amazonaws.com/manage-device-files/ -F "file1=@directory/filename1.extension" -F "file2=@directory/filename2.extension" -F "serial_number=db4734761a9d5a8d1f98d9e7fc383996" 
