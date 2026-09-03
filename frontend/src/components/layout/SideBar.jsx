import {
  Box, Drawer,
} from '@mui/material';
import React from 'react';
import SidebarMenuItems from './SidebarMenuItems';

const SideBar = ({
  mobileOpen,
  handleDrawerToggle
}) => {

  return (<>
    {/* desktop sidebar */}
    <Box
      className='sidebar'
      component={"aside"}
      position="fixed"
      top="70px"
      left="0"
      bottom="0"
      height={"100vh - 70px"}
      width="290px"
      boxShadow={3}
      sx={{
        background: "white",
        overflowY: "auto",
        display: { xs: 'none', md: 'block' },
        scrollbarColor: "",
          "&::-webkit-scrollbar, & *::-webkit-scrollbar": {
            backgroundColor: "white.light",
            width: "5px",
            borderRadius: 8,
          },
          "&::-webkit-scrollbar-thumb, & *::-webkit-scrollbar-thumb": {
            borderRadius: 8,
            backgroundColor: "primary.main",
          },
          "&::-webkit-scrollbar-thumb:focus, & *::-webkit-scrollbar-thumb:focus": {
            backgroundColor: "secondary.main",
          }
      }}
    >
      <SidebarMenuItems />
    </Box>

    {/* mobile sidebar */}
    <Drawer
      variant="temporary"
      open={mobileOpen}
      onClose={handleDrawerToggle}
      ModalProps={{
        keepMounted: true,
      }}
      sx={{
        display: { xs: 'block', sm: 'none' }
      }}
    >
      <SidebarMenuItems />
    </Drawer>
  </>
  )
}

export default SideBar;