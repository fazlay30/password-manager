"use client"

import { useToast } from "@/hooks/toast"
import { groupMemberList } from "@/lib/api"
import { Box, Grid, Paper, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Typography } from "@mui/material"
import { useParams } from "next/navigation"
import { useEffect, useState } from "react"

// export const metadata = {
//   title: 'HashLock - Dashboard',
// }

const GroupMembers = () => {
  const [groupMembersData, setGroupMembersData] = useState([])
  const {id} = useParams()
  const { showToast } = useToast()

  // group members
  const fetchGroupMembers = async () => {
    try {
      const response = await groupMemberList(id)
      setGroupMembersData(response?.data)
      
    } catch (err) {
      showToast("Group member fetch error!", "error")
    }
  }

  useEffect(() => {
    id && fetchGroupMembers()
  }, [])

  return (
    <>
      <Grid container spacing={3}>
        <Grid item xs={12} md={5}>
          <Typography variant="h2" fontSize="30px" fontWeight="700" mb={2}>
            Group Members
          </Typography>
        </Grid>
        <Grid item xs={12} md={7}>
          
        </Grid>
      </Grid>
      <Box>
        <TableContainer component={Paper}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>EMail</TableCell>
                <TableCell>Description</TableCell>
                <TableCell>Role</TableCell>
                <TableCell>Status</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {groupMembersData?.length > 0 ? 
                groupMembersData?.map((groupMemberItem, index) => (
                  <TableRow key={index}>
                    <TableCell>{groupMemberItem?.email}</TableCell>
                    <TableCell>{groupMemberItem?.description}</TableCell>
                    <TableCell>
                      {
                        groupMemberItem?.fk_role_id === 1 ? "Admin"
                        : groupMemberItem?.fk_role_id === 2 ? "Editor"
                        : "Viewer"
                      }
                    </TableCell>
                    <TableCell>{groupMemberItem?.status}</TableCell>
                  </TableRow>
                ))
                : <Box p={3}><p>No item found!</p></Box>
              }
            </TableBody>
          </Table>
        </TableContainer>
      </Box>
    </>
  )
}

export default GroupMembers