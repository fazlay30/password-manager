import React from 'react'
import { Box, Button, Typography } from '@mui/material'
import { Formik } from 'formik'
import TextFieldCommon from './TextFieldCommon'
import { addGroup, editGroup } from '@/lib/api'
import { useToast } from '@/hooks/toast'
import { GroupAddEdit } from '@/data/groupFields'

const AddEditGroup = ({
  action = 'add',
  setOpen,
  defaultValues,
  fetchGroups
}) => {
  const { showToast } = useToast()

  const handleClose = () => setOpen(false)

  // initial field values
  const initialValues = {
    name: defaultValues?.name ?? "",
    description: defaultValues?.description ?? "",
  }

  // validation
  const handleValidateForm = (values) => {
    const errors = {}
    if (!values.name) {
      errors.name = 'Required'
    } else if (!values.description) {
      errors.description = 'Required'
    }
    return errors
  }

  // submit form
  const handleSubmitForm = async (values, setSubmitting) => {
    try {
      // edit
      if (action === 'edit') {
        const updateResponse = await editGroup(defaultValues?.id, values)
        if (updateResponse?.success) {
          showToast("Group updated successfully!", "success")
          fetchGroups()
          handleClose()
        }
        setSubmitting(false)
      } else if (action === 'add') {
        const addResponse = await addGroup(values)
        if (addResponse?.success) {
          showToast("Group added successfully!", "success")
          fetchGroups()
          handleClose()
        }
        setSubmitting(false)
      }
    } catch (err) {
      showToast("Group action failed!", "error")
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
            GroupAddEdit.map((field, i) => (
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

export default AddEditGroup