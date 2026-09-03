import React from 'react'
import { Modal, Box, Typography, IconButton } from '@mui/material'
import { FaRegWindowClose } from "react-icons/fa"

const CommonModal = ({ open, onClose, title, children }) => {
  return (
    <Modal
      open={open}
      onClose={onClose}
      aria-labelledby="modal-title"
      aria-describedby="modal-description"
      disableScrollLock={true}
    >
      <Box
        sx={{
          position: 'absolute',
          top: '50%',
          left: '50%',
          transform: 'translate(-50%, -50%)',
          width: 400,
          maxHeight: "80vh",
          bgcolor: 'background.paper',
          borderRadius: 2,
          boxShadow: 24,
          p: 4,
          overflow: "auto",
        }}
      >
        <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 2 }}>
          <Typography id="modal-title" variant="h6" component="h2">
            {title}
          </Typography>
          <IconButton onClick={onClose}>
            <FaRegWindowClose />
          </IconButton>
        </Box>
        <Box>
          {children}
        </Box>
      </Box>
    </Modal>
  )
}

export default CommonModal
