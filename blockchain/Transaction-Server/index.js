require("dotenv").config();
const express = require("express");
const { ethers } = require("ethers");
const cors = require("cors");
const fs = require("fs");
const app = express();
app.use(express.json());
app.use(cors());

const abi = require("./abi/PasswordManager.json");
const provider = new ethers.JsonRpcProvider(process.env.RPC_URL);
const wallet = new ethers.Wallet(process.env.PRIVATE_KEY, provider);
const contract = new ethers.Contract(process.env.CONTRACT_ADDRESS, abi, wallet);

function serializeBigInts(obj) {
  return JSON.parse(JSON.stringify(obj, (_, v) => (typeof v === "bigint" ? v.toString() : v)));
}

// Save user credential
app.post("/user/save", async (req, res) => {
  const { credentialID, userID, userName, password, domain, website } = req.body;
  try {
    const tx = await contract.saveUserCredential(credentialID, userID, userName, password, domain, website);
    await tx.wait();
    res.json({ success: true, txHash: tx.hash });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Update user credential
app.put("/user/update", async (req, res) => {
  const { credentialID, userID, userName, password, domain, website } = req.body;
  try {
    const tx = await contract.updateUserCredential(credentialID, userID, userName, password, domain, website);
    await tx.wait();
    res.json({ success: true, txHash: tx.hash });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Remove user credential
app.delete("/user/delete", async (req, res) => {
  try {
    const { credentialID, userID } = req.body;
    const tx = await contract.removeUserCredential(credentialID, userID);
    await tx.wait();
    res.json({ success: true, txHash: tx.hash });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Save group credential
app.post("/group/save", async (req, res) => {
  const { groupProjectCredentialID, groupProjectID, userName, password, domain, website } = req.body;
  try {
    const tx = await contract.saveGroupCredential(
      groupProjectCredentialID,
      groupProjectID,
      userName,
      password,
      domain,
      website
    );
    await tx.wait();
    res.json({ success: true, txHash: tx.hash });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Update group credential
app.put("/group/update", async (req, res) => {
  const { groupProjectCredentialID, groupProjectID, userName, password, domain, website } = req.body;
  try {
    const tx = await contract.updateGroupCredential(
      groupProjectCredentialID,
      groupProjectID,
      userName,
      password,
      domain,
      website
    );
    await tx.wait();
    res.json({ success: true, txHash: tx.hash });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Remove group credential
app.delete("/group/delete", async (req, res) => {
  const { groupProjectCredentialID, groupProjectID } = req.body;
  try {
    const tx = await contract.removeGroupCredential(groupProjectCredentialID, groupProjectID);
    await tx.wait();
    res.json({ success: true, txHash: tx.hash });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});


const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`🚀 Server running on port ${PORT}`);
});
