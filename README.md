# SQL Database with PHP Webpage and API Access

This repository contains my PHP code for facilitating interaction with an SQL database, hosted on an EC2 instance. There are two methods in which a user may interact with the database: a simple webpage designed for ease of human use, and the API.

# Schema

The schema for the database is relatively simple. There is a 'devices' table where each row represents a single device with 5 columns: device_id (auto incrementing ID), type (laptop, phone, car, etc), brand (microsoft, apple, toyota, etc), serial_number, and status (active or inactive). In addition, there is a 'device_file_paths' table where each row represents a PDF file with 3 columns: file_id (auto incrementing ID), file_path, and device_id (foreign key representing the device with which this PDF is associated). 

# Webpage

The webpage can be accessed by visiting the server address in a browser, https://ec2-3-91-97-58.compute-1.amazonaws.com/ (demo pictures below if you're hesitant to go clicking links willy nilly). My main goal was to provide a clear interface with which a human can perform data queries that would otherwise require handtyped (and potentially damaging) SQL commands.

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

 _requires at least one of:_
  
    POST serial_number
    POST brand
    POST type
    POST status (valid inputs: "active", "inactive")
   
_optional:_

    POST exact (valid inputs: "true", "false" - defaults to "false" - determines whether query uses relation 'LIKE' (false) or '=' (true) )
    POST return_columns (valid input: comma deliminated list of columns - defaults to returning every column)
    
_output:_

    {
    "num_rows": number_of_returned_rows,
    device_id_1{"column_name_1":"column_value_1","column_name_2":"column_value_2","column_name_3":"column_value_3",...},
    device_id_2{"column_name_1":"column_value_1","column_name_2":"column_value_2","column_name_3":"column_value_3",...},
    device_id_3{"column_name_1":"column_value_1","column_name_2":"column_value_2","column_name_3":"column_value_3",...},
    ...
    }
