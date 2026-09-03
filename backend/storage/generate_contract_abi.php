<?php
/**
 * Script to generate a proper ABI without validation errors
 * Run with: php generate_contract_abi.php > storage/contract_abi.json
 */

// Your contract's ABI - this should be generated from your Solidity contract
// You can get this from Remix, Truffle, or Hardhat when compiling the contract
$abi = [
    // saveUserCredential function
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "userCredentialID", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "userID", "type" => "uint256"],
            ["internalType" => "string", "name" => "userName", "type" => "string"],
            ["internalType" => "string", "name" => "password", "type" => "string"],
            ["internalType" => "string", "name" => "domain", "type" => "string"]
        ],
        "name" => "saveUserCredential",
        "outputs" => [],
        "stateMutability" => "nonpayable",
        "type" => "function"
    ],
    // getUserCredential function
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "userCredentialID", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "userID", "type" => "uint256"]
        ],
        "name" => "getUserCredential",
        "outputs" => [
            [
                "components" => [
                    ["internalType" => "string", "name" => "userName", "type" => "string"],
                    ["internalType" => "string", "name" => "password", "type" => "string"],
                    ["internalType" => "string", "name" => "domain", "type" => "string"],
                    ["internalType" => "uint256", "name" => "version", "type" => "uint256"]
                ],
                "internalType" => "struct PasswordManager.Credential",
                "name" => "",
                "type" => "tuple"
            ]
        ],
        "stateMutability" => "view",
        "type" => "function"
    ],
    // saveGroupCredential function
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "groupProjectCredentialID", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "groupProjectID", "type" => "uint256"],
            ["internalType" => "string", "name" => "userName", "type" => "string"],
            ["internalType" => "string", "name" => "password", "type" => "string"],
            ["internalType" => "string", "name" => "domain", "type" => "string"]
        ],
        "name" => "saveGroupCredential",
        "outputs" => [],
        "stateMutability" => "nonpayable",
        "type" => "function"
    ],
    // getGroupCredential function
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "groupProjectCredentialID", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "groupProjectID", "type" => "uint256"]
        ],
        "name" => "getGroupCredential",
        "outputs" => [
            [
                "components" => [
                    ["internalType" => "string", "name" => "userName", "type" => "string"],
                    ["internalType" => "string", "name" => "password", "type" => "string"],
                    ["internalType" => "string", "name" => "domain", "type" => "string"],
                    ["internalType" => "uint256", "name" => "version", "type" => "uint256"]
                ],
                "internalType" => "struct PasswordManager.Credential",
                "name" => "",
                "type" => "tuple"
            ]
        ],
        "stateMutability" => "view",
        "type" => "function"
    ],
    // updateUserCredential function
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "userCredentialID", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "userID", "type" => "uint256"],
            ["internalType" => "string", "name" => "userName", "type" => "string"],
            ["internalType" => "string", "name" => "password", "type" => "string"],
            ["internalType" => "string", "name" => "domain", "type" => "string"]
        ],
        "name" => "updateUserCredential",
        "outputs" => [],
        "stateMutability" => "nonpayable",
        "type" => "function"
    ],
    // updateGroupCredential function
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "groupProjectCredentialID", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "groupProjectID", "type" => "uint256"],
            ["internalType" => "string", "name" => "userName", "type" => "string"],
            ["internalType" => "string", "name" => "password", "type" => "string"],
            ["internalType" => "string", "name" => "domain", "type" => "string"]
        ],
        "name" => "updateGroupCredential",
        "outputs" => [],
        "stateMutability" => "nonpayable",
        "type" => "function"
    ],
    // removeUserCredential function
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "userCredentialID", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "userID", "type" => "uint256"]
        ],
        "name" => "removeUserCredential",
        "outputs" => [],
        "stateMutability" => "nonpayable",
        "type" => "function"
    ],
    // removeGroupCredential function
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "groupProjectCredentialID", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "groupProjectID", "type" => "uint256"]
        ],
        "name" => "removeGroupCredential",
        "outputs" => [],
        "stateMutability" => "nonpayable",
        "type" => "function"
    ],
    // getAllUserCredentialsByUserID function - properly formatted array of struct output
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "userID", "type" => "uint256"]
        ],
        "name" => "getAllUserCredentialsByUserID",
        "outputs" => [
            [
                "components" => [
                    ["internalType" => "uint256", "name" => "credentialID", "type" => "uint256"],
                    ["internalType" => "string", "name" => "userName", "type" => "string"],
                    ["internalType" => "string", "name" => "password", "type" => "string"],
                    ["internalType" => "string", "name" => "domain", "type" => "string"],
                    ["internalType" => "uint256", "name" => "version", "type" => "uint256"]
                ],
                "internalType" => "struct PasswordManager.CredentialWithID[]",
                "name" => "",
                "type" => "tuple[]"
            ]
        ],
        "stateMutability" => "view",
        "type" => "function"
    ],
    // getAllGroupCredentialsByGroupProjectID function - properly formatted array of struct output
    [
        "inputs" => [
            ["internalType" => "uint256", "name" => "groupProjectID", "type" => "uint256"]
        ],
        "name" => "getAllGroupCredentialsByGroupProjectID",
        "outputs" => [
            [
                "components" => [
                    ["internalType" => "uint256", "name" => "credentialID", "type" => "uint256"],
                    ["internalType" => "string", "name" => "userName", "type" => "string"],
                    ["internalType" => "string", "name" => "password", "type" => "string"],
                    ["internalType" => "string", "name" => "domain", "type" => "string"],
                    ["internalType" => "uint256", "name" => "version", "type" => "uint256"]
                ],
                "internalType" => "struct PasswordManager.CredentialWithID[]",
                "name" => "",
                "type" => "tuple[]"
            ]
        ],
        "stateMutability" => "view",
        "type" => "function"
    ],
    // UserCredentialSaved event
    [
        "anonymous" => false,
        "inputs" => [
            ["indexed" => true, "internalType" => "uint256", "name" => "credentialID", "type" => "uint256"],
            ["indexed" => true, "internalType" => "uint256", "name" => "userID", "type" => "uint256"]
        ],
        "name" => "UserCredentialSaved",
        "type" => "event"
    ],
    // UserCredentialUpdated event
    [
        "anonymous" => false,
        "inputs" => [
            ["indexed" => true, "internalType" => "uint256", "name" => "credentialID", "type" => "uint256"],
            ["indexed" => true, "internalType" => "uint256", "name" => "userID", "type" => "uint256"]
        ],
        "name" => "UserCredentialUpdated",
        "type" => "event"
    ],
    // UserCredentialRemoved event
    [
        "anonymous" => false,
        "inputs" => [
            ["indexed" => true, "internalType" => "uint256", "name" => "credentialID", "type" => "uint256"],
            ["indexed" => true, "internalType" => "uint256", "name" => "userID", "type" => "uint256"]
        ],
        "name" => "UserCredentialRemoved",
        "type" => "event"
    ],
    // GroupCredentialSaved event
    [
        "anonymous" => false,
        "inputs" => [
            ["indexed" => true, "internalType" => "uint256", "name" => "credentialID", "type" => "uint256"],
            ["indexed" => true, "internalType" => "uint256", "name" => "groupID", "type" => "uint256"]
        ],
        "name" => "GroupCredentialSaved",
        "type" => "event"
    ],
    // GroupCredentialUpdated event
    [
        "anonymous" => false,
        "inputs" => [
            ["indexed" => true, "internalType" => "uint256", "name" => "credentialID", "type" => "uint256"],
            ["indexed" => true, "internalType" => "uint256", "name" => "groupID", "type" => "uint256"]
        ],
        "name" => "GroupCredentialUpdated",
        "type" => "event"
    ],
    // GroupCredentialRemoved event
    [
        "anonymous" => false,
        "inputs" => [
            ["indexed" => true, "internalType" => "uint256", "name" => "credentialID", "type" => "uint256"],
            ["indexed" => true, "internalType" => "uint256", "name" => "groupID", "type" => "uint256"]
        ],
        "name" => "GroupCredentialRemoved",
        "type" => "event"
    ]
];

// Output as JSON
echo json_encode($abi, JSON_PRETTY_PRINT);