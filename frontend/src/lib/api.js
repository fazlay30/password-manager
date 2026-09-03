import axios from "./axios"


// user credentials
export const getUserCredentials = async () => {
  try {
    const response = await axios.get('/api/user-credentials')
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const getSingleUserCreds = async (id) => {
  try {
    const response = await axios.get(`/api/user-credentials/${id}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const addUserCred = async (payload) => {
  try {
    const response = await axios.post('/api/user-credentials', payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const editUserCred = async (id, payload) => {
  try {
    const response = await axios.post(`/api/user-credentials/update/${id}`, payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const deleteUserCred = async (id) => {
  try {
    const response = await axios.post(`/api/user-credentials/delete/${id}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

// Groups
export const getGroupList = async () => {
  try {
    const response = await axios.get('/api/group-projects')
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const getSingleGroupInfo = async (id) => {
  try {
    const response = await axios.get(`/api/group-projects/${id}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const addGroup = async (payload) => {
  try {
    const response = await axios.post('/api/group-projects', payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const editGroup = async (id, payload) => {
  try {
    const response = await axios.post(`/api/group-projects/update/${id}`, payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const deleteGroup = async (id) => {
  try {
    const response = await axios.post(`/api/group-projects/delete/${id}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const sendInvitation = async (id, payload) => {
  try {
    const response = await axios.post(`/api/group-projects/invite/${id}`, payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const acceptInvitation = async (userId, token) => {
  try {
    const response = await axios.get(`/api/group-projects/verify-invite/${userId}/${token}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const groupMemberList = async (id) => {
  try {
    const response = await axios.get(`/api/group-projects/member-list/${id}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const groupCredentials = async (id) => {
  try {
    const response = await axios.get(`/api/group-project/${id}/credentials`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const addGroupCredential = async (id, payload) => {
  try {
    const response = await axios.post(`/api/group-project/${id}/credentials`, payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const editGropCred = async (grpId, credId, payload) => {
  try {
    const response = await axios.post(`/api/group-project/${grpId}/credentials/update/${credId}`, payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const deleteGropCred = async (grpId, credId) => {
  try {
    const response = await axios.post(`/api/group-project/${grpId}/credentials/delete/${credId}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const leaveGroup = async (id) => {
  try {
    const response = await axios.get(`/api/group-projects/leave/${id}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

// user update
export const updateProfile = async (payload) => {
  try {
    const response = await axios.post(`/api/user/profile-update`, payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const updatePassword = async (payload) => {
  try {
    const response = await axios.post(`/api/user/change-password`, payload)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

// search
export const searchCredential = async (param) => {
  try {
    const response = await axios.get(`/api/user-credentials/search/${param}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

export const searchGroup = async (param) => {
  try {
    const response = await axios.get(`/api/group-projects/search/${param}`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

// history
export const actionHistories = async (id) => {
  try {
    const response = await axios.get(`/api/group-projects/${id}/action-histories`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}

// notification
export const getNotifications = async () => {
  try {
    const response = await axios.get(`/api/user/notifications/all`)
    return response.data
  } catch (error) {
    console.error('Error:', error)
    throw error
  }
}