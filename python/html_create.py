import json
import boto3
import data_modification

def lambda_handler(event, context):
    
    # get the csv data from the event
    csv_data = event.get('csv_data', '')
    if not csv_data:
        return {
            'statusCode': 400,
            'body': 'CSV data is required'
        }

    client = boto3.client('dynamodb')
    dynamodb_table_name = 'http-data-table'

    # put all data into a dynamodb table
    try:        
        # csv data = title, website_url, description
        for item in csv_data:
            client.put_item(
                TableName=dynamodb_table_name,
                Item={
                    'id': {'S': data_modification.generate_unique_id()},  # Replace with your logic to generate unique IDs
                    'csv_data': {'S': item[0]},
                    'website_url': {'S': item[1]},
                    'description': {'S': item[2]}
                }
            )
        
        return {
            'statusCode': 200,
            'body': json.dumps({'message': 'CSV data processed successfully'})
        }
    except Exception as e:
        return {
            'statusCode': 500,
            'body': json.dumps({'error': str(e)})
        }