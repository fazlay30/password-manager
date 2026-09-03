import { Box } from "@mui/material"
import React, { useState } from "react"
import Header from "./Header"
import SideBar from "./SideBar"
import Footer from "./Footer"

const Layout = ({ children }) => {
  // sidebar mobile
  const [mobileOpen, setMobileOpen] = useState(false)

  const handleDrawerToggle = () => {
    setMobileOpen(!mobileOpen)
  }

  return (
    <Box sx={{
      marginTop: "70px",
      marginLeft: {
        md: "290px"
      },
      position: "relative"
    }}>
      <Header
        handleDrawerToggle={handleDrawerToggle}
      />
      <SideBar
        mobileOpen={mobileOpen}
        handleDrawerToggle={handleDrawerToggle}
      />
      <Box sx={{ p: { xs: "20px", md: "36px" }, minHeight: "calc(100vh - 130px)" }}>
        {children}
      </Box>
      <Footer />
    </Box>
  )
}

export default Layout
