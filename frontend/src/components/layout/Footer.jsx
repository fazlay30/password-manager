import { Box, Typography } from '@mui/material';
import React from 'react';

const Footer = () => {
  return (
    <Box
      sx={{
        background: "white",
        p: "20px",
        borderTop: "1px solid #ddd"
      }}
    >
      <Typography
        textAlign={"center"}
        fontSize={"15px"}
        sx={{color: "secondary.main"}}
      >
        &copy; {new Date().getFullYear()} | All Rights Reserved - Hashlock
      </Typography>
    </Box>
  )
}

export default Footer;