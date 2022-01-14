<?php

class ReadCsvFile
{
    protected $csvRowData = [];

    /**
     * read the csv and put it into array
     */
    public function readCsv()
    {
        if (!empty($this->csvRowData)) {
            return;
        }

        if (($handle = fopen('mockdata.csv', 'r')) !== FALSE) {
            $key = 0;
            while(($csvData = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $this->csvRowData[$key]['id'] = $csvData[0];
                $this->csvRowData[$key]['first_name'] = $csvData[1];
                $this->csvRowData[$key]['last_name'] = $csvData[2];
                $this->csvRowData[$key]['email'] = $csvData[3];
                $this->csvRowData[$key]['country'] = $csvData[4];
                $this->csvRowData[$key]['ip_address'] = $csvData[5];
                $key++;
            }
            fclose($handle);
        } else {
            throw new \Exception("Unable to open csv");
        }
    }

    /**
     * get the total number of user is in each country
     */
    public function getNumberOfUsersPerCountry()
    {
        $this->readCsv();
        $numberOfUsersPerCountry = [];
        foreach($this->csvRowData as $data) {
            if (!isset($numberOfUsersPerCountry[$data['country']])) {
                $numberOfUsersPerCountry[$data['country']] = $data['country'];
                $totalCount = array_count_values(array_column($this->csvRowData, 'country'))[$data['country']];
                echo "{$data['country']} has {$totalCount} user \n";
            }
        }
    }

    /** get the country name which has the most number of user */
    public function getCompanyNameForMostOftheUserWorksFor()
    {
        $this->readCsv();
        $companyNameForMostOftheUserWorksFor = [];
        foreach($this->csvRowData as $data) {
            if (!isset($companyNameForMostOftheUserWorksFor[$data['country']])) {
                $totalCount = array_count_values(array_column($this->csvRowData, 'country'))[$data['country']];
                $companyNameForMostOftheUserWorksFor[$data['country']] = $totalCount;
            }
        }
        asort($companyNameForMostOftheUserWorksFor);
        echo array_key_last($companyNameForMostOftheUserWorksFor) . " country has the most number of user (" . end($companyNameForMostOftheUserWorksFor) . ")";
    }

    /**
     * print the user name and thier country as a greet
     */
    public function greet()
    {
        $this->readCsv();
        foreach($this->csvRowData as $data) {
            echo "Hello I am " . $data['first_name'] . " " . $data['last_name'] . " I am from " . $data['country']; echo "\n";
        }

    }

    /** get the details from ip address */
    public function getGeoIp($ip)
    {
        $url = "https://api.ipinfodb.com/v3/ip-country/?format=json&key=3a351e2b706f80228b633eb436bd4a4df99ab4abd2b1f732db4ca796a7a554dc&ip=" . $ip;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
        
        print_r($result);
    }
}

try {
    $readCsvObj = new ReadCsvFile();
    $readCsvObj->readCsv();
    $readCsvObj->getNumberOfUsersPerCountry();
    $readCsvObj->getCompanyNameForMostOftheUserWorksFor();
    $readCsvObj->greet();
    $readCsvObj->getGeoIp("188.41.141.172");
} catch (\Exception $ex) {
    echo $ex->getMessage();
}

