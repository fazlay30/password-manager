'use client'

import CommonButton from '@/components/common/Button';
import CommonModal from '@/components/common/CommonModal';
import UpdatePasswordForm from '@/components/common/UpdatePasswordForm';
import UpdateProfileForm from '@/components/common/UpdateProfileForm';
import { useStorage } from '@/hooks/storage';
import { Box, Grid, Typography } from '@mui/material'
import React, { useState } from 'react'

const Profile = () => {
  const [openModal, setOpenModal] = useState(false)
  const [openPasswordModal, setOpenPasswordModal] = useState(false)
  const { getItem } = useStorage('localStorage');
  const user = getItem('user');

  const handleClose = () => setOpenModal(false)
  const handlePasswordClose = () => setOpenPasswordModal(false)

  return (
    <>
      <Grid container spacing={3}>
        <Grid item xs={12} md={8}>
          <Typography variant="h2" fontSize="30px" fontWeight="700">
            My Profile
          </Typography>
        </Grid>
        <Grid item xs={12} md={4}>
          <Box sx={{textAlign: {xs: "center", md: "right"}}}>
            <CommonButton
              text="Update Profile"
              onClick = {() => setOpenModal(true)}
            />
            {!user?.google_id &&
            <CommonButton
              text="Update Password"
              onClick = {() => setOpenPasswordModal(true)}
              sx={{ml: 1}}
            />}
          </Box>
        </Grid>
      </Grid>
      <Typography mb={1} mt={1}><b>Name:</b> {user?.name}</Typography>
      <Typography><b>Email:</b> {user?.email}</Typography>

      {/* edit profile modal */}
      {openModal && <CommonModal open={open} onClose={handleClose} title="Edit Profile">
        <UpdateProfileForm setOpen={setOpenModal} />
      </CommonModal>}

      {/* update password modal */}
      {openPasswordModal && <CommonModal open={open} onClose={handlePasswordClose} title="Update Password">
        <UpdatePasswordForm setOpen={setOpenPasswordModal} />
      </CommonModal>}
    </>
  )
}

export default Profile