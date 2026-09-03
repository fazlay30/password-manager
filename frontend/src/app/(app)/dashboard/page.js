"use client"

import { Box, Grid, Typography, Button, Menu, MenuItem } from "@mui/material";
import { useEffect, useState } from "react";
import AddEditForm from "@/components/common/addEditForm";
import CommonButton from "@/components/common/Button";
import CommonModal from "@/components/common/CommonModal";
import CredentialItemBox from "@/components/common/CredentialItemBox";
import SearchFieldBox from "@/components/common/SearchFieldBox";
import { useToast } from "@/hooks/toast";
import { getUserCredentials, searchCredential } from "@/lib/api";
import { useRouter } from "next/navigation";

const Dashboard = () => {
  const [userCredentials, setUserCredentials] = useState();
  const [openModal, setOpenModal] = useState(false);
  const [sortAnchorEl, setSortAnchorEl] = useState(null);
  const [viewAnchorEl, setViewAnchorEl] = useState(null); // State for view dropdown
  const [sortOption, setSortOption] = useState("none");
  const [viewType, setViewType] = useState("list");
  const { showToast } = useToast();
  const router = useRouter();
  
  const savedCallbackUrl = localStorage.getItem('callback_url');

  // Add sorting functionality
  const handleSortClick = (event) => {
    setSortAnchorEl(event.currentTarget);
  };

  const handleSortClose = () => {
    setSortAnchorEl(null);
  };

  const handleSortSelect = (option) => {
    setSortOption(option);
    sortCredentials(option);
    handleSortClose();
  };

  const sortCredentials = (option) => {
    if (!userCredentials || userCredentials.length === 0) return;
    
    let sortedCreds = [...userCredentials];
    
    console.log('sortedCreds', sortedCreds);
    switch(option) {
      case "nameAsc":
        sortedCreds.sort((a, b) => a.site_name.localeCompare(b.site_name));
        break;
      case "nameDesc":
        sortedCreds.sort((a, b) => b.site_name.localeCompare(a.site_name));
        break;
      case "dateAsc":
        sortedCreds.sort((a, b) => a.id - b.id);
        break;
      case "dateDesc":
        sortedCreds.sort((a, b) => b.id - a.id);
        break;
      default:
        // No sorting or default
        break;
    }
    
    setUserCredentials(sortedCreds);
  };

  const fetchUserCreds = async () => {
    try {
      const response = await getUserCredentials();
      const credentials = response?.data;
      
      setUserCredentials(credentials);
      if (sortOption !== "none") {
        sortCredentials(sortOption);
      }
      
      if (response?.message?.statusText == "Group project invitation accepted successfully!") {
        showToast(response?.message?.statusText, "success");
        localStorage.removeItem('callback_url');
      }
      else if (savedCallbackUrl) {
        if (typeof window !== 'undefined') {
          const sanitizedUrl = savedCallbackUrl.replace(/["]+/g, '');
          const callbackUrlItems = sanitizedUrl.split('/');
    
          if (callbackUrlItems.includes('accept-invitation')) {
            router.push(sanitizedUrl);
          }
        }
      }
    } catch (err) {
      showToast("User Credentials fetch error!", "error");
    }
  };

  useEffect(() => {
    fetchUserCreds();
  }, []);

  const handleClose = () => setOpenModal(false);

  // Function to handle view type selection (List, Tiles)
  const handleViewSelect = (view) => {
    setViewType(view);
    setViewAnchorEl(null); // Close the menu after selection
  };

  // Function to handle view dropdown opening
  const handleViewClick = (event) => {
    setViewAnchorEl(event.currentTarget);
  };

  const handleViewClose = () => {
    setViewAnchorEl(null);
  };

  return (
    <>
      <Grid container spacing={3} alignItems="center">
        <Grid item xs={12} md={6}>
          <Typography variant="h2" fontSize="30px" fontWeight="700">
            My Credentials
          </Typography>
        </Grid>
        <Grid item xs={12} md={2}>
          <Box sx={{ textAlign: { xs: "center", md: "right" } }}>
            <CommonButton
              text="Create Credentials"
              onClick={() => setOpenModal(true)}
            />
          </Box>
        </Grid>
        <Grid item xs={12} md={2}>
          <SearchFieldBox apiFunc={searchCredential} setSearchResult={setUserCredentials} />
        </Grid>

        {/* Wrap Sort and View buttons in a container with flexbox */}
        <Grid item xs={12} md={2}>
          <Box display="flex" justifyContent="flex-end" alignItems="center">
            {/* Sort Button */}
            <Button
              variant="contained"
              color="primary"
              onClick={handleSortClick}
              sx={{ minWidth: 'auto', px: 1, marginRight: 2 }}
            >
              Sort &#8645;
            </Button>
            <Menu
              anchorEl={sortAnchorEl}
              open={Boolean(sortAnchorEl)}
              onClose={handleSortClose}
            >
              <MenuItem onClick={() => handleSortSelect("nameAsc")}>Name (A-Z)</MenuItem>
              <MenuItem onClick={() => handleSortSelect("nameDesc")}>Name (Z-A)</MenuItem>
              <MenuItem onClick={() => handleSortSelect("dateAsc")}>Date (Oldest First)</MenuItem>
              <MenuItem onClick={() => handleSortSelect("dateDesc")}>Date (Newest First)</MenuItem>
            </Menu>

            {/* View Button with Dropdown */}
            <Button
              variant="contained"
              color="primary"
              onClick={handleViewClick}
              sx={{ minWidth: 'auto', px: 1 }}
            >
              View &#8645;
            </Button>
            <Menu
              anchorEl={viewAnchorEl}
              open={Boolean(viewAnchorEl)}
              onClose={handleViewClose}
            >
              <MenuItem onClick={() => handleViewSelect("list")}>List</MenuItem>
              <MenuItem onClick={() => handleViewSelect("tiles")}>Tiles</MenuItem>
            </Menu>
          </Box>
        </Grid>
      </Grid>

      <Grid container spacing={3} mt={1} className={viewType}>
        {userCredentials?.length > 0 ?
          userCredentials?.map((credItem, index) => (
            <Grid item xs={12} md={viewType === "tiles" ? 4 : 12} key={index}>
              <CredentialItemBox credItem={credItem} fetchUserCreds={fetchUserCreds} />
            </Grid>
          ))
          : <Box p={3}><p>No item found!</p></Box>
        }
      </Grid>

      {/* add cred modal */}
      {openModal && <CommonModal open={open} onClose={handleClose} title="Add Credential">
        <AddEditForm action={"add"} setOpen={setOpenModal} fetchUserCreds={fetchUserCreds} />
      </CommonModal>}
    </>
  );
};

export default Dashboard;
