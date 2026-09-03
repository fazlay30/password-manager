'use client'

import React from 'react'
import { Box, Grid2, InputBase, Typography } from "@mui/material"
import { useState } from "react"
import { FaCopy } from "react-icons/fa6"
import { FaEdit } from 'react-icons/fa'
import { MdDeleteOutline } from 'react-icons/md'
import { useToast } from '@/hooks/toast'
import CommonModal from './CommonModal'
import CommonButton from './Button'
import AddEditForm from './addEditForm'
import { deleteGropCred, deleteUserCred } from '@/lib/api'

// Confirmation Modal Component
const ConfirmationModal = ({ open, onClose, onConfirm, title, message }) => {
  return (
    <CommonModal open={open} onClose={onClose} title={title}>
      <Box sx={{ p: 2 }}>
        <Typography variant="body1" mb={3}>{message}</Typography>
        <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 2 }}>
          <CommonButton
            text="Cancel"
            onClick={onClose}
          />
          <CommonButton
            text="Delete"
            color="error"
            onClick={onConfirm}
          />
        </Box>
      </Box>
    </CommonModal>
  );
};

const CredentialItemBox = ({credItem, fetchUserCreds, myRole=''}) => {
  const [showPassword, setShowPassword] = useState(false)
  const [openModal, setOpenModal] = useState(false)
  const [confirmDeleteModal, setConfirmDeleteModal] = useState(false)
  const { showToast } = useToast()

  const copyPassword = () => {
    navigator.clipboard.writeText(credItem?.password ?? "")
    showToast("Password copied to clipboard!", "success")
  }

  const editPassword = () => {
    setOpenModal(true)
  }

  // Show confirmation modal before deleting
  const handleDeleteClick = () => {
    setConfirmDeleteModal(true)
  }

  // Execute the actual deletion after confirmation
  const executeDelete = async () => {
    if (credItem?.group_project) {
      // delete group cred
      try {
        const deleteResponse = await deleteGropCred(credItem?.fk_group_project_id, credItem?.id)
        if (deleteResponse?.success) {
          showToast("Credential deleted successfully!", "success")
          fetchUserCreds()
        }
      } catch (err) {
        console.log(err)
        
        showToast("Credential action failed!", "error")
      }
    } else {
      try {
        const deleteResponse = await deleteUserCred(credItem?.id,)
        if (deleteResponse?.success) {
          showToast("Credential deleted successfully!", "success")
          fetchUserCreds()
        }
      } catch (err) {
        showToast("Credential action failed!", "error")
      }
    }
    setConfirmDeleteModal(false)
  }

  const handleClose = () => setOpenModal(false)
  const closeConfirmModal = () => setConfirmDeleteModal(false)

  // verify site url
  const verifyUrl = (url) => {
    if (url?.startsWith("http")) {
      return url
    } else {
      return `http://${url}`
    }
  }

  return (
    <Box
      sx={{
        border: "1px solid",
        borderColor: "grey.300",
        borderRadius: 2,
        padding: 3,
        bgcolor: "background.paper",
        boxShadow: 1,
      }}
    >
      <Typography variant="h6" color="text.primary" mb={0}>
        <a href={verifyUrl(credItem?.site_url)} target="_blank">{credItem?.site_name}</a>
      </Typography>
      <Typography component="p" color="text.primary" mb={1}>
        Link: <a href={verifyUrl(credItem?.site_url)} target="_blank">{credItem?.site_url}</a>
      </Typography>
      <Typography variant="body2" color="text.secondary" mb={1}>
        <strong>Email/Phone/Username:</strong> {credItem?.username}
      </Typography>
      <Box
        sx={{
          display: "flex",
          alignItems: "bottm",
          gap: 1,
        }}
      >
        <Typography variant="body2" color="text.secondary">Password:</Typography>
        <InputBase
          type={showPassword ? "text" : "password"}
          variant="outlined"
          size="small"
          sx={{border: 'none', paddingBottom: 0}}
          value={credItem?.password || ""}
        />
      </Box>
      <Grid2 container spacing={1} mt={1}>
        <CommonButton
          text = {`${showPassword ? 'Hide' : 'Show'} Password`}
          onClick = {() => setShowPassword((prevState) => !prevState)}
        />
        <CommonButton
          color = "success"
          icon = {<FaCopy fontSize="18px" />}
          onClick={copyPassword}
        />
        {myRole !== 'Viewer' && <>
          <CommonButton
            color = "warning"
            icon = {<FaEdit fontSize="18px" />}
            onClick={editPassword}
          />
          <CommonButton
            color = "error"
            icon = {<MdDeleteOutline fontSize="18px" />}
            onClick={handleDeleteClick} // Changed to show confirmation modal first
          />
        </>}
      </Grid2>

      {/* edit cred modal */}
      {openModal && <CommonModal open={openModal} onClose={handleClose} title="Edit Credential">
        <AddEditForm
          action={credItem?.group_project ? "editGrpCred" : "edit"}
          setOpen={setOpenModal}
          defaultValues={credItem}
          fetchUserCreds={fetchUserCreds}
        />
      </CommonModal>}
      
      {/* Confirmation Delete Modal */}
      <ConfirmationModal
        open={confirmDeleteModal}
        onClose={closeConfirmModal}
        onConfirm={executeDelete}
        title="Delete Credential"
        message="Are you sure you want to delete this credential? This action cannot be undone."
      />
    </Box>
  )
}

export default CredentialItemBox