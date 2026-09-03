"use client"

import AddEditGroup from "@/components/common/AddEditGroup"
import CommonButton from "@/components/common/Button"
import CommonModal from "@/components/common/CommonModal"
import GroupItemBox from "@/components/common/GroupItemBox"
import SearchFieldBox from "@/components/common/SearchFieldBox"
import { useToast } from "@/hooks/toast"
import { getGroupList, searchGroup } from "@/lib/api"
import { Box, Grid, Typography } from "@mui/material"
import { useEffect, useState } from "react"

// export const metadata = {
//   title: 'HashLock - Dashboard',
// }

const Groups = () => {
  const [groupList, setGroupList] = useState([])
    const [openModal, setOpenModal] = useState(false)
  const { showToast } = useToast()

  const fetchGroups = async () => {
    try {
      const response = await getGroupList()
      setGroupList(response?.data)
      
    } catch (err) {
      showToast("Group fetch error!", "error")
    }
  }

  useEffect(() => {
    fetchGroups()
  }, [])

  const handleClose = () => setOpenModal(false)

  return (
    <>
      <Grid container spacing={3}>
        <Grid item xs={12} md={6}>
          <Typography variant="h2" fontSize="30px" fontWeight="700">
            My Groups
          </Typography>
        </Grid>
        <Grid item xs={12} md={3}>
          <Box sx={{textAlign: {xs: "center", md: "right"}}}>
            <CommonButton
              text="Create Group"
              onClick = {() => setOpenModal(true)}
            />
          </Box>
        </Grid>
        <Grid item xs={12} md={3}>
          <SearchFieldBox
            apiFunc={searchGroup}
            setSearchResult={setGroupList}
          />
        </Grid>
      </Grid>
      <Grid container spacing={3} mt={1}>
          {groupList?.length > 0 ? 
            groupList?.map((groupItem, index) => (
              <Grid item xs={12} md={4} key={index}>
                <GroupItemBox groupItem={groupItem} />
              </Grid>
            ))
            : <Box p={3}><p>No item found!</p></Box>
          }
      </Grid>

      {/* add group modal */}
      {openModal && <CommonModal open={open} onClose={handleClose} title="Add Group">
        <AddEditGroup action={"add"} setOpen={setOpenModal} fetchGroups={fetchGroups} />
      </CommonModal>}
    </>
  )
}

export default Groups