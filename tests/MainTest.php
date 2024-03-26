<?php

use Lodipay\DTO\Tests\DTO\Document;
use Lodipay\DTO\Tests\DTO\TestDTO;

describe('fromArray tests', function () {
    test('main test', function () {
        $dto = TestDTO::from([
            'name' => 'Test Test',
            'register_id' => 'УУ12234456',
            'notneeded' => 'asdf',
            'dateFromTimestamp' => 1664094320,
            'date' => '10-29-2020 12:44:01',
            'child' => [
                'id' => 5,
                'title' => 'haha',
                'createdAt' => '2022-09-29T05:17:11Z',
                'notneeded' => 'asdf',
                'status' => 'accepted',
            ],
            'children' => [
                [
                    'id' => 5,
                    'title' => 'haha',
                    'createdAt' => '2022-09-29T05:17:11Z',
                    'notneeded' => 'asdf',
                    'status' => 'accepted',
                ],
                [
                    'id' => 6,
                    'title' => 'haha 2',
                    'createdAt' => '2022-09-29T05:17:11Z',
                    'notneeded' => 'asdf',
                    'status' => 'accepted',
                ],
                [
                    'id' => 7,
                    'title' => 'haha 3',
                    'createdAt' => '2022-09-29T05:17:11Z',
                    'notneeded' => 'asdf',
                ],
            ],
            'dateNull' => null,
        ]);

        expect($dto)
            ->name->toBe('Test Test')
            ->registerNumber->toBe('УУ12234456')
            ->dateFromTimestamp->format('Y-m-d H:i:s')->toBe('2022-09-25 08:25:20')
            ->date->format('Y-m-d H:i:s')->toBe('2020-10-29 12:44:01')
            ->children->toHaveCount(3)
        ;

        expect($dto->child)
            ->id->toBe(5)
            ->title->toBe('haha')
            ->createdAt->format('Y-m-d H:i:s')->toBe('2022-09-29 05:17:11')
        ;

        $arr = $dto->toArray();

        expect($arr)
            ->not()->toHaveKeys(['age'])
            ->toMatchArray([
                'name' => 'Test Test',
                'register_number' => 'УУ12234456',
                'dateFromTimestamp' => '2022-09-25T08:25:20+00:00',
                'date' => '2020-10-29T12:44:01+00:00',
                'dateNull' => null,
                'child' => [
                    'id' => 5,
                    'name' => 'haha',
                    'createdAt' => '2022-09-29 05:17:11',
                    'status' => 'accepted',
                ],
                'children' => [
                    [
                        'id' => 5,
                        'name' => 'haha',
                        'createdAt' => '2022-09-29 05:17:11',
                        'status' => 'accepted',
                    ],
                    [
                        'id' => 6,
                        'name' => 'haha 2',
                        'createdAt' => '2022-09-29 05:17:11',
                        'status' => 'accepted',
                    ],
                    [
                        'id' => 7,
                        'name' => 'haha 3',
                        'createdAt' => '2022-09-29 05:17:11',
                        'status' => 'pending',
                    ],
                ],
            ])
        ;
    });
});

describe('XML serialization', function () {
    it('should serialize to XML', function () {
        $dto = TestDTO::from([
            'name' => 'Test Test',
            'firstName' => 'Baavai',
            'register_id' => 'УУ12234456',
            'notneeded' => 'asdf',
            'dateFromTimestamp' => 1664094320,
            'date' => '10-29-2020 12:44:01',
            'child' => [
                'id' => 5,
                'title' => 'haha',
                'createdAt' => '2022-09-29T05:17:11Z',
                'notneeded' => 'asdf',
                'status' => 'accepted',
            ],
            'children' => [
                [
                    'id' => 5,
                    'title' => 'haha',
                    'createdAt' => '2022-09-29T05:17:11Z',
                    'notneeded' => 'asdf',
                    'status' => 'accepted',
                ],
                [
                    'id' => 6,
                    'title' => 'haha 2',
                    'createdAt' => '2022-09-29T05:17:11Z',
                    'notneeded' => 'asdf',
                    'status' => 'accepted',
                ],
                [
                    'id' => 7,
                    'title' => 'haha 3',
                    'createdAt' => '2022-09-29T05:17:11Z',
                    'notneeded' => 'asdf',
                ],
            ],
            'dateNull' => null,
        ]);

        expect($dto->serialize('xml'))->toBe('<?xml version="1.0"?>
<response><name>Test Test</name><firstName>Baavai</firstName><register_number>&#x423;&#x423;12234456</register_number><date>2020-10-29T12:44:01+00:00</date><dateFromTimestamp>2022-09-25T08:25:20+00:00</dateFromTimestamp><dateNull/><child><id>5</id><name>haha</name><createdAt>2022-09-29 05:17:11</createdAt><status>accepted</status></child><children><id>5</id><name>haha</name><createdAt>2022-09-29 05:17:11</createdAt><status>accepted</status></children><children><id>6</id><name>haha 2</name><createdAt>2022-09-29 05:17:11</createdAt><status>accepted</status></children><children><id>7</id><name>haha 3</name><createdAt>2022-09-29 05:17:11</createdAt><status>pending</status></children></response>
');
    });

    it('should deserialize to XML', function () {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Document>
   <GrpHdr>
      <MsgId>87fbf20130425/1</MsgId>
      <CreDtTm>2014-10-21T11:16:58</CreDtTm>
      <TxsCd>1001</TxsCd>
      <NbOfTxs>1</NbOfTxs>
      <CtrlSum>1000</CtrlSum>
      <InitgPty>
         <Id>
            <OrgId>
               <AnyBIC>19899</AnyBIC>
            </OrgId>
         </Id>
      </InitgPty>
      <Crdtl>
         <Lang>0</Lang>
         <LoginID>110011</LoginID>
         <RoleID>1</RoleID>
         <Pwds>
            <PwdType>1</PwdType>
            <Pwd>1313213</Pwd>
         </Pwds>
         <Pwds>
            <PwdType>2</PwdType>
            <Pwd>1313213</Pwd>
         </Pwds>
      </Crdtl>
   </GrpHdr>
   <PmtInf>
      <NbOfTxs>1</NbOfTxs>
      <CtrlSum>1000</CtrlSum>
      <ForT>F</ForT>
      <Dbtr>
         <Nm>Grapectiy Mongolia LLC</Nm>
      </Dbtr>
      <DbtrAcct>
         <Id>
            <IBAN>EE481012345678901234</IBAN>
         </Id>
         <Ccy>EUR</Ccy>
      </DbtrAcct>
      <CdtTrfTxInf>
         <Amt>
            <InstdAmt>1000</InstdAmt>
         </Amt>
         <Cdtr>
            <Nm>Bayar</Nm>
         </Cdtr>
         <CdtrAcct>
            <Id>
               <IBAN>EE212200223456789102</IBAN>
            </Id>
            <Ccy>EUR</Ccy>
         </CdtrAcct>
         <CdtrAgt>
            <FinInstnId>
               <BICFI>TDBM</BICFI>
            </FinInstnId>
            s
         </CdtrAgt>
         <RmtInf>
            <AddtlRmtInf>Гүйлгээний утга</AddtlRmtInf>
         </RmtInf>
      </CdtTrfTxInf>
   </PmtInf>
</Document>
XML;
        $document = Document::from($xml, 'xml');

        expect($document->GrpHdr)
            ->MsgId->toBe('87fbf20130425/1')
            ->CreDtTm->format('Y-m-d H:i:s')->toBe('2014-10-21 11:16:58')
            ->TxsCd->toBe('1001')
            ->NbOfTxs->toBe(1)
            ->CtrlSum->toBe(1000)
            ->InitgPtyIdOrgIdAnyBIC->toBe('19899')
        ;
    });
});
