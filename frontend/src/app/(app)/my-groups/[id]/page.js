"use client"

import AddEditForm from "@/components/common/addEditForm"
import AddEditGroup from "@/components/common/AddEditGroup"
import CommonButton from "@/components/common/Button"
import CommonModal from "@/components/common/CommonModal"
import CredentialItemBox from "@/components/common/CredentialItemBox"
import HistoryContent from "@/components/common/HistoryContent"
import InviteMemberForm from "@/components/common/InviteMemberForm"
import { useStorage } from "@/hooks/storage"
import { useToast } from "@/hooks/toast"
import { getSingleGroupInfo, groupCredentials, leaveGroup } from "@/lib/api"
import { Box, Grid, Typography } from "@mui/material"
import Link from "next/link"
import { useParams, useRouter } from "next/navigation"
import { useEffect, useState } from "react"

// export const metadata = {
//   title: 'HashLock - Dashboard',
// }

const GroupDetails = () => {
  const [groupInfo, setGroupInfo] = useState({})
  const [groupCredList, setGroupCredList] = useState([])
  const [myRole, setMyRole] = useState('')
  const [openModal, setOpenModal] = useState(false)
  const [openCredAddModal, setOpenCredAddModal] = useState(false)
  const [openInviteModal, setOpenInviteModal] = useState(false)
  const [openHistoryModal, setpenHistoryModal] = useState(false)
  const {id} = useParams();
  const { showToast } = useToast()
  const router = useRouter()

  // user info
  const { getItem } = useStorage('localStorage')
  const user = getItem('user')

  // group details
  const fetchGroupDetails = async () => {
    try {
      const response = await getSingleGroupInfo(id)
      setGroupInfo(response?.data)
      setMyRole(response?.data?.user_role)
    } catch (err) {
      showToast("Group info fetch error!", "error")
    }
  }

  // group credentials
  const fetchGroupCreds = async () => {
    try {
      const response = await groupCredentials(id)
      setGroupCredList(response?.data)
    } catch (err) {
      showToast("Group credential fetch error!", "error")
    }
  }

  useEffect(() => {
    id && fetchGroupDetails()
    id && fetchGroupCreds()
  }, [])

  const handleClose = () => setOpenModal(false)
  const handleCloseAddCredModal = () => setOpenCredAddModal(false)
  const handleInviteModalClose = () => setOpenInviteModal(false)
  const handleHistoryModalClose = () => setpenHistoryModal(false)

  // leave group
  const leaveGroupAction = async () => {
    try {
      await leaveGroup(id)
      showToast("Group left successfully!", "success")
      router.push("/my-groups")
    } catch (err) {
      showToast("Failed to leave group!", "error")
    }
  }

  return (
    <>
      <Grid container spacing={3}>
        <Grid item xs={12} md={4}>
          <Typography variant="h2" fontSize="30px" fontWeight="700">
            {groupInfo?.name}
          </Typography>
        </Grid>
        <Grid item xs={12} md={8}>
          <Box sx={{textAlign: {xs: "center", md: "right"}}}>
            {myRole !== 'Viewer' && <>
              <CommonButton
                text="Add Credential"
                onClick = {() => setOpenCredAddModal(true)}
              />
            </>}
            {myRole !== 'Owner' && <>
              <CommonButton
                text="Leave Group"
                onClick = {leaveGroupAction}
                sx={{marginLeft: 1}}
              />
            </>}
            {myRole !== 'Viewer' && <>
              <CommonButton
                text="Invite Member"
                onClick = {() => setOpenInviteModal(true)}
                sx={{marginLeft: 1}}
              />
              {myRole !== 'Manager' && <>
                <CommonButton
                  text="History"
                  onClick = {() => setpenHistoryModal(true)}
                  sx={{marginLeft: 1}}
                />
                {user?.id === groupInfo?.fk_user_id &&
                  <>
                    <CommonButton
                      text="Edit Group"
                      onClick = {() => setOpenModal(true)}
                      sx={{marginLeft: 1}}
                    />
                  </>
                }
              </>}
              <Link href={`/my-groups/${id}/members`}>
                <CommonButton
                  text="View Members"
                  onClick = {() => {}}
                  sx={{marginLeft: 1}}
                />
              </Link>
            </>}
          </Box>
        </Grid>
      </Grid>
      <Typography color="#666" mt={"8px"}>{groupInfo?.description}</Typography>
      <Grid container spacing={3} mt={1}>
        {groupCredList?.length > 0 ? 
          groupCredList?.map((credItem, index) => (
            <Grid item xs={12} md={4} key={index}>
              <CredentialItemBox credItem={credItem} fetchUserCreds={fetchGroupCreds} myRole={myRole} />
            </Grid>
          ))
          : <Box p={3}><p>No credential item found!</p></Box>
        }
      </Grid>

      {/* edit group modal */}
      {openModal && <CommonModal open={open} onClose={handleClose} title="Edit Group">
        <AddEditGroup action={"edit"} setOpen={setOpenModal} defaultValues={groupInfo} fetchGroups={fetchGroupDetails} />
      </CommonModal>}

      {/* add cred modal */}
      {openCredAddModal && <CommonModal open={openCredAddModal} onClose={handleCloseAddCredModal} title="Add Credential">
        <AddEditForm action={"addGrpCred"} setOpen={setOpenCredAddModal} fetchUserCreds={fetchGroupCreds} id={id} />
      </CommonModal>}

      {/* invite member modal */}
      {openInviteModal && <CommonModal open={openInviteModal} onClose={handleInviteModalClose} title="Invite Member">
        <InviteMemberForm setOpen={setOpenInviteModal} id={id} myRole={myRole} />
      </CommonModal>}

      {/* history modal */}
      {openHistoryModal && <CommonModal open={openHistoryModal} onClose={handleHistoryModalClose} title="History">
        <HistoryContent id={id} />
      </CommonModal>}
    </>
  )
}

export default GroupDetails