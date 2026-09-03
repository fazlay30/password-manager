import React, { useEffect } from 'react'
import { Box, Button, FormControl, MenuItem, Select, Typography } from '@mui/material'
import { Formik } from 'formik'
import TextFieldCommon from './TextFieldCommon'
import { sendInvitation } from '@/lib/api'
import { useToast } from '@/hooks/toast'
import { InvitationFields } from '@/data/invitationFields'

const InviteMemberForm = ({
  setOpen,
  id=null,
  myRole
}) => {
  const { showToast } = useToast()

  const handleClose = () => setOpen(false)

  useEffect(() => {

  }, [myRole])

  const initialValues = {
    email: "",
    fk_role_id: 1,
    description: "",
  }

  // validation
  const handleValidateForm = (values) => {
    const errors = {}
    if (!values.email) {
      errors.email = 'Required'
    } else if (!values.fk_role_id) {
      errors.fk_role_id = 'Required'
    } else if (!values.description) {
      errors.description = 'Required'
    }
    return errors
  }

  // submit form
  const handleSubmitForm = async (values, setSubmitting) => {
    try {
      const addResponse = await sendInvitation(id, values)
      if (addResponse?.success) {
        showToast("Invitation send successfully!", "success")
        handleClose()
      }
      setSubmitting(false)
    } catch (err) {
      showToast("Invitation action failed!", "error")
      setSubmitting(false)
    }
  }

  return (
    <Formik
      initialValues={initialValues}
      validate={values => handleValidateForm(values)}
      onSubmit={(values, { setSubmitting }) => handleSubmitForm(values, setSubmitting)}
    >
      {({
        values,
        errors,
        touched,
        handleChange,
        handleBlur,
        handleSubmit,
        isSubmitting,
        setFieldValue
      }) => (
        <Box
          component="form"
          onSubmit={handleSubmit}
          mb={2}
        >
          {
            InvitationFields.map((field, i) => (
              field?.type === "file" ?
                <Box key={i} mb={2}>
                  <Typography fontWeight="500" sx={{ mb: 1 }}>{field.label}:</Typography>
                  <input
                    name={field.name}
                    type="file"
                    onChange={(event) => {
                      setFieldValue(field?.name, event.currentTarget.files[0])
                    }}
                  />
                  <Typography component={"span"} color={"primary.main"}>
                    {errors[field.name] && touched[field.name] && errors[field.name]}
                  </Typography>
                </Box>
                : field?.type === "select" ?
                <Box key={i} mb={2}>
                  <FormControl
                    fullWidth
                    variant="standard"
                    margin="normal"
                    >
                      <Select
                        name={field.name}
                        value={values[field.name]}
                        onChange={handleChange}
                        label="Role"
                      >
                        {myRole !== "Manager" && 
                          <MenuItem value={1}>Admin</MenuItem>
                        }
                        <MenuItem value={2}>Editor</MenuItem>
                        <MenuItem value={3}>Viewer</MenuItem>
                      </Select>
                    </FormControl>
                  <Typography component={"span"} color={"primary.main"}>
                    {errors[field.name] && touched[field.name] && errors[field.name]}
                  </Typography>
                </Box> :
                <Box key={i} mb={2}>
                  <TextFieldCommon
                    id={field.id}
                    label={field.label}
                    variant={'outlined'}
                    type={field.type}
                    name={field.name}
                    requiredStatus={field.required}
                    disabledStatus={field.disabled ?? false}
                    value={values[field.name]}
                    onchangeInputValue={handleChange}
                  />
                  <Typography component={"span"} color={"primary.main"}>
                    {errors[field.name] && touched[field.name] && errors[field.name]}
                  </Typography>
                </Box>
            ))
          }
          <Button
            variant="contained"
            type="submit"
            color="primary"
            disabled={isSubmitting}
            sx={{mt: 2, width: '100%'}}
          >Submit</Button>
        </Box>
      )}
    </Formik>
  )
}

export default InviteMemberForm