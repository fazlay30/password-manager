import React, { useState } from 'react';
import {
  AppBar,
  Avatar,
  Box,
  Button,
  Container,
  IconButton,
  Menu,
  MenuItem,
  Toolbar,
  Tooltip,
  Typography
} from "@mui/material";
import { FaAngleDown, FaBell } from "react-icons/fa";
import { TiThMenuOutline } from "react-icons/ti";
import { useStorage } from '@/hooks/storage';
import CommonModal from '../common/CommonModal';
import NotificationContent from '../common/NotificationContent';

const Header = ({handleDrawerToggle}) => {
  const [anchorElUser, setAnchorElUser] = useState(null)
  const [openNotification, setOpenNotification] = useState(false)
    const { getItem } = useStorage('localStorage')
    const user = getItem('user')

  // user dropdown actions
  const handleOpenUserMenu = (event) => {
    setAnchorElUser(event.currentTarget);
  };
  const handleCloseUserMenu = () => {
    setAnchorElUser(null);
  };

  const handleNotifyModalClose = () => setOpenNotification(false)

  return (
    <AppBar
      position="fixed"
      top="0"
      left="0"
      right="0"
      sx={{py: {xs: 1, md: 0}, minHeight: '70px'}}
    >
      <Container maxWidth="2xl">
        <Toolbar disableGutters>
          <Typography
            variant="h6"
            noWrap
            component="a"
            href="#"
            sx={{
              mr: 2,
              display: { xs: 'none', md: 'flex' },
              minWidth: "250px"
            }}
          >
            <img
              src={'/images/logo.jpg'}
              alt="logo"
              width={150}
              height={50}
            />
          </Typography>

          {/* left menu button mobile */}
          <Box sx={{ flexGrow: 1, display: { xs: 'flex', md: 'none' } }}>
            <IconButton
              size="large"
              onClick={handleDrawerToggle}
              color="inherit"
            >
              <TiThMenuOutline />
            </IconButton>
          </Box>

          {/* mobile logo */}
          <Typography
            variant="h5"
            noWrap
            component="a"
            href="#"
            sx={{
              mr: 2,
              display: { xs: 'flex', md: 'none' },
              flexGrow: 1,
            }}
          >
            <img
              src={'/images/logo.jpg'}
              alt="logo"
              width={120}
              height={36}
            />
          </Typography>

          {/* profile dropdown */}
          <Box sx={{width: {md: "100%"}, display: 'flex', gap: 1, alignItems: 'center', justifyContent: 'end'}}>
            <Button sx={{px: 0}} onClick = {() => setOpenNotification(true)}>
              <FaBell style={{color: 'white', width: '20px', height: '20px'}} />
            </Button>
            <Typography component="h4" fontWeight="600" fontSize="18px">{user?.name}</Typography>
            {/* <Tooltip title="Open settings">
              <IconButton
                onClick={handleOpenUserMenu}
                sx={{
                  p: 0,
                  display: "flex",
                  columnGap: 2,
                  alignItems: "center",
                  borderRadius: 0
                }}
              >
                <Avatar alt="profile" src="/images/dummy-user.png" />
                <Box sx={{ display: {xs: "none", md: "block"} }}>
                  <Typography
                    sx={{
                      color: "white.light",
                      fontSize: 14,
                      textAlign: "left"
                    }}
                  >
                    Vendor
                  </Typography>
                  <Typography
                    sx={{
                      color: "white.main",
                      fontWeight: 600
                    }}
                  >
                    name <FaAngleDown />
                  </Typography>
                </Box>
              </IconButton>
            </Tooltip>
            <Menu
              sx={{ mt: '45px' }}
              id="menu-appbar"
              anchorEl={anchorElUser}
              anchorOrigin={{
                vertical: 'top',
                horizontal: 'right',
              }}
              keepMounted
              transformOrigin={{
                vertical: 'top',
                horizontal: 'right',
              }}
              open={Boolean(anchorElUser)}
              onClose={handleCloseUserMenu}
            >
              {ProfileNavItems.map((item, i) => (
                <MenuItem key={i} onClick={handleCloseUserMenu}>
                  <Typography textAlign="center">
                    <Link href={item.path}>{item.name}</Link>
                  </Typography>
                </MenuItem>
              ))}
              <MenuItem>
                <Typography textAlign="center">
                  Logout
                </Typography>
              </MenuItem>
            </Menu> */}
          </Box>

        </Toolbar>
      </Container>

      {/* notify modal */}
      {openNotification && <CommonModal open={openNotification} onClose={handleNotifyModalClose} title="Notification">
        <NotificationContent />
      </CommonModal>}
    </AppBar>
  );
}

export default Header;